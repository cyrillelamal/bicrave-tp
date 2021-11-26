<?php

namespace App\Controller;

use App\Entity\Order;
use App\Security\Role;
use App\Security\Voter\OrderVoter;
use App\UseCase\Reservation\GetOrderReservations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/orders', name: 'order_')]
class OrderController extends AbstractController
{
    private GetOrderReservations $getOrderReservations;

    public function __construct(
        GetOrderReservations $getOrderReservations,
    )
    {
        $this->getOrderReservations = $getOrderReservations;
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

    #[Route(name: 'create', methods: [Request::METHOD_POST])]
    #[IsGranted(Role::CUSTOMER)]
    public function create(): Response
    {
        return new Response(); // TODO: charge the customer
    }
}
