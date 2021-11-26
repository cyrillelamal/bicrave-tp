<?php

namespace App\Controller;

use App\Exception\Product\ProductNotFoundException;
use App\UseCase\Product\GetProductById;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/products', name: 'product_')]
class ProductController extends AbstractController
{
    private GetProductById $getProductById;

    public function __construct(
        GetProductById $getProductById,
    )
    {
        $this->getProductById = $getProductById;
    }

    #[Route(path: '/{id<\d+>}', name: 'read')]
    public function read(int $id): Response
    {
        try {
            $product = ($this->getProductById)($id);

            return $this->render('product/read.html.twig', [
                'product' => $product,
            ]);
        } catch (ProductNotFoundException) {
            throw new NotFoundHttpException();
        }
    }
}
