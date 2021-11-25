<?php

namespace App\Controller;

use App\Entity\Product;
use App\Infrastructure\SubroutineInterface;
use App\Message\Product\GetNoveltiesMessage;
use App\Message\Product\GetPopularProductsMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    private SubroutineInterface $subroutine;

    public function __construct(
        SubroutineInterface $subroutine,
    )
    {
        $this->subroutine = $subroutine;
    }

    #[Route(name: 'index', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        /** @var Product[] $novelties */
        $novelties = $this->subroutine->execute(new GetNoveltiesMessage());
        /** @var Product[] $popular */
        $popular = $this->subroutine->execute(new GetPopularProductsMessage());

        return $this->render('home_page/index.html.twig', [
            'novelties' => $novelties,
            'popular_products' => $popular,
        ]);
    }
}
