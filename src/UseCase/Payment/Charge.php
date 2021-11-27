<?php

namespace App\UseCase\Payment;

use App\Common\Cart\CartManagerInterface;
use App\Common\Cart\CartProviderInterface;
use App\Entity\Cart;
use App\Entity\Demand;
use App\Entity\Order;
use App\Entity\Reservation;
use App\Event\OrderCreatedEvent;
use App\Exception\Product\ProductsOutOfStockException;
use App\Service\Payment\Exception\PaymentFailedException;
use App\Service\Payment\PaymentProcessorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Charge the current user with the contents of his/her shopping cart.
 */
final class Charge
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private CartProviderInterface $cartProvider;
    private PaymentProcessorInterface $paymentProcessor;
    private CartManagerInterface $cartManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        LoggerInterface           $logger,
        EntityManagerInterface    $entityManager,
        CartProviderInterface     $cartProvider,
        PaymentProcessorInterface $paymentProcessor,
        CartManagerInterface      $cartManager,
        EventDispatcherInterface  $eventDispatcher,
    )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->cartProvider = $cartProvider;
        $this->paymentProcessor = $paymentProcessor;
        $this->cartManager = $cartManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Charge the current user with the contents of his/her shopping cart.
     *
     * @return Order the formed order.
     * @throws PaymentFailedException when the payment processor fails.
     * @throws ProductsOutOfStockException when there are products that are out of stock.
     */
    public function __invoke(array $params = []): Order
    {
        try {
            $this->entityManager->beginTransaction();

            $cart = $this->getCart();

            $order = $this->formOrder($cart);

            $this->paymentProcessor->charge($order, $params);

            $this->cartManager->clear($cart);

            $this->entityManager->commit();
        } catch (ProductsOutOfStockException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->warning('Transaction failed', ['exception' => $e]);
            $this->entityManager->rollback();
            $this->entityManager->clear();
            throw new PaymentFailedException();
        }

        $this->eventDispatcher->dispatch(new OrderCreatedEvent($order), OrderCreatedEvent::NAME);

        return $order;
    }

    /**
     * Make order using the shopping cart content.
     *
     * @param Cart $cart
     * @return Order
     * @throws ProductsOutOfStockException when there are products that are out of stock.
     */
    private function formOrder(Cart $cart): Order
    {
        $this->logger->debug('Forming order', ['cart' => $cart]);

        $order = new Order();
        $order->setCustomer($cart->getOwner());

        $reservations = $this->reserveDemands($order, ...$cart->getDemands()->toArray());
        $this->logger->debug('Made reservations', ['reservation' => $reservations]);
        foreach ($reservations as $reservation) {
            $order->addReservation($reservation);
        }

        $this->logger->debug('Formed order', ['order' => $order]);
        return $order;
    }

    /**
     * Make reservations of demands.
     *
     * @param Order $order reservations can't exist without the order.
     * @param Demand ...$demands demands to reserve.
     * @return Reservation[]
     * @throws ProductsOutOfStockException when there are products that are out of stock.
     */
    private function reserveDemands(Order $order, Demand ...$demands): array
    {
        /** @var Collection<Demand> $unrealizable */
        /** @var Collection<Demand> $demands */
        [$unrealizable, $demands] = (new ArrayCollection($demands))
            ->filter(fn(Demand $demand) => !$demand->isUseless())
            ->partition(fn($_, Demand $demand) => $demand->isOutOfStock());

        if (!$unrealizable->isEmpty()) {
            throw new ProductsOutOfStockException($unrealizable->toArray());
        }

        return $demands->map(function (Demand $demand) use ($order) {
            $reservation = Reservation::of($demand, $order);
            $reservation->apply();
            return $reservation;
        })->toArray();
    }

    private function getCart(): Cart
    {
        return $this->cartProvider->getCart();
    }
}
