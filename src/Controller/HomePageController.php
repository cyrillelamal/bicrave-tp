<?php

namespace App\Controller;

use App\UseCase\Product\GetNovelties;
use App\UseCase\Product\GetPopularProducts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    private GetNovelties $getNovelties;
    private GetPopularProducts $getPopularProducts;

    public function __construct(
        GetNovelties       $getNovelties,
        GetPopularProducts $getPopularProducts,
    )
    {
        $this->getNovelties = $getNovelties;
        $this->getPopularProducts = $getPopularProducts;
    }

    #[Route(name: 'index', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        $novelties = ($this->getNovelties)();
        $popularProducts = ($this->getPopularProducts)();

        return $this->render('home_page/index.html.twig', [
            'novelties' => $novelties,
            'popular_products' => $popularProducts,
        ]);
    }
}
