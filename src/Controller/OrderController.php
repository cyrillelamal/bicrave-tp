<?php

namespace App\Controller;

use App\Entity\Order;
use App\Security\Role;
use App\Security\Voter\OrderVoter;
use App\Service\Payment\StripePaymentProcessor;
use App\UseCase\Payment\Charge;
use App\UseCase\Reservation\GetOrderReservations;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[Route(path: '/orders', name: 'order_')]
class OrderController extends AbstractController
{
    private LoggerInterface $logger;
    private GetOrderReservations $getOrderReservations;
    private Charge $charge;
    private TranslatorInterface $translator;

    public function __construct(
        LoggerInterface      $logger,
        GetOrderReservations $getOrderReservations,
        Charge               $charge,
        TranslatorInterface  $translator,
    )
    {
        $this->logger = $logger;
        $this->getOrderReservations = $getOrderReservations;
        $this->charge = $charge;
        $this->translator = $translator;
    }

    #[Route(path: '/{id<\d+>}', name: 'read', methods: [Request::METHOD_GET])]
    #[IsGranted(OrderVoter::READ, 'order')]
    public function read(Order $order): Response
    {
        $reservations = ($this->getOrderReservations)($order);

        return $this->render('order/read.html.twig', [
            'order' => $order,
            'reservations' => $reservations,
        ]);
    }

    /**
     * @throws Throwable -> 500
     */
    #[Route(name: 'create', methods: [Request::METHOD_POST], schemes: ['%secure_channel%', 'https'])]
    #[IsGranted(Role::CUSTOMER)]
    public function create(Request $request): Response
    {
        $stripeToken = $request->request->get(StripePaymentProcessor::SOURCE_KEY);
        if (null === $stripeToken) {
            $this->logger->warning('No stripe token provided', ['request' => $request->request->all()]);
            throw new UnprocessableEntityHttpException();
        }

        $order = ($this->charge)([
            StripePaymentProcessor::SOURCE_KEY => $stripeToken,
        ]);

        $success = $this->translator->trans('payment.order.accepted', ['%number%' => $order->getNumber()]);
        $this->addFlash('success', $success);

        return $this->redirectToRoute('profile_index');
    }
}
