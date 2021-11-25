<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Infrastructure\SubroutineInterface;
use App\Message\Category\GetCategoryTreesMessage;
use App\Message\Product\PaginateCategoryProductsMessage;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/categories', name: 'category_')]
class CategoryController extends AbstractController
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
        /** @var Category[] $categories */
        $categories = $this->subroutine->execute(new GetCategoryTreesMessage());

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'read', methods: [Request::METHOD_GET])]
    public function read(Category $category): Response
    {
        /** @var PaginationInterface<Product> $products */
        $products = $this->subroutine->execute(new PaginateCategoryProductsMessage($category->getId()));

        return $this->render('category/read.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
