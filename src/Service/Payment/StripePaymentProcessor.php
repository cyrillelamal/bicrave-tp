<?php

namespace App\Service\Payment;

use App\Common\Currency\Currency;
use App\Common\Currency\CurrencyProviderInterface;
use App\Common\Order\Status;
use App\Entity\Order;
use App\Service\Payment\Exception\PaymentFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripePaymentProcessor implements PaymentProcessorInterface
{
    public const SOURCE_KEY = 'stripeToken';

    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private CurrencyProviderInterface $currencyProvider;
    private string $stripeSecret;

    public function __construct(
        LoggerInterface           $logger,
        EntityManagerInterface    $entityManager,
        CurrencyProviderInterface $currencyProvider,
        string                    $stripeSecret,
    )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->currencyProvider = $currencyProvider;
        $this->stripeSecret = $stripeSecret;
    }

    /**
     * {@inheritDoc}
     */
    public function charge(Order $order, array $params = []): void
    {
        if (!array_key_exists(self::SOURCE_KEY, $params)) {
            $this->logger->warning('No provided stripe token', ['params' => $params]);
            throw new PaymentFailedException('No provided stripe token');
        }

        $params = [
            'amount' => $order->getTotal(),
            'currency' => $this->getCurrency()->getCode(),
            'source' => $params[self::SOURCE_KEY] ?? '',
            'description' => $order->getDescription(),
        ];

        try {
            $this->logger->debug('Creating charge', ['params' => $params]);
            Stripe::setApiKey($this->stripeSecret);
            Charge::create($params);

            $order->setStatus(Status::ACCEPTED);

            $this->entityManager->persist($order);
        } catch (ApiErrorException $e) {
            $this->logger->error('Charge failed', ['params' => $params, 'exception' => $e]);
            throw new PaymentFailedException('Payment failed', previous: $e);
        }
    }

    protected function getCurrency(): Currency
    {
        return $this->currencyProvider->getCurrency();
    }
}
