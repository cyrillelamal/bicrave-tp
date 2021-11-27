<?php

namespace App\Controller;

use App\Security\Role;
use App\UseCase\Order\PaginateUserOrders;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profiles', name: 'profile_')]
class ProfileController extends AbstractController
{
    private PaginateUserOrders $paginateUserOrders;

    public function __construct(
        PaginateUserOrders $paginateUserOrders,
    )
    {
        $this->paginateUserOrders = $paginateUserOrders;
    }

    #[Route(name: 'index')]
    #[IsGranted(Role::CUSTOMER)]
    public function index(): Response
    {
        $orders = ($this->paginateUserOrders)();

        return $this->render('profile/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
