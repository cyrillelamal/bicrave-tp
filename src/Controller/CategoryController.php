<?php

namespace App\Controller;

use App\Entity\Category;
use App\UseCase\Category\GetCategoryTrees;
use App\UseCase\Product\PaginateCategoryProducts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/categories', name: 'category_')]
class CategoryController extends AbstractController
{
    private GetCategoryTrees $getCategoryTrees;
    private PaginateCategoryProducts $paginateCategoryProducts;

    public function __construct(
        GetCategoryTrees         $getCategoryTrees,
        PaginateCategoryProducts $paginateCategoryProducts,
    )
    {
        $this->getCategoryTrees = $getCategoryTrees;
        $this->paginateCategoryProducts = $paginateCategoryProducts;
    }

    #[Route(name: 'index', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        $categories = ($this->getCategoryTrees)();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'read', methods: [Request::METHOD_GET])]
    public function read(Category $category): Response
    {
        $products = ($this->paginateCategoryProducts)($category);

        return $this->render('category/read.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
