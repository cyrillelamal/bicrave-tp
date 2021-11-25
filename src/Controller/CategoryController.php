<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/categories', name: 'category_')]
class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    #[Route(name: 'index', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        $categories = $this->categoryRepository->getCategoryTrees();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route(path: '/{id}', name: 'read', methods: [Request::METHOD_GET])]
    public function read(Category $category): Response
    {
        return $this->render('category/read.html.twig', [
            'category' => $category,
        ]);
    }
}
