<?php

namespace App\Controller;

use App\Common\Cart\CartResource;
use App\Entity\Product;
use App\UseCase\Cart\PickUpProductFromCart;
use App\UseCase\Cart\PutProductInCart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(path: '/carts', name: 'cart_')]
class CartController extends AbstractController
{
    private PutProductInCart $putProductInCart;
    private PickUpProductFromCart $pickUpProductFromCart;
    private CartResource $resource;

    public function __construct(
        PutProductInCart      $putProductInCart,
        PickUpProductFromCart $pickUpProductFromCart,
        CartResource          $resource,
    )
    {
        $this->putProductInCart = $putProductInCart;
        $this->pickUpProductFromCart = $pickUpProductFromCart;
        $this->resource = $resource;
    }

    #[Route(name: 'index', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/{id<\d+>}', name: 'put_product', methods: [Request::METHOD_PATCH, Request::METHOD_PUT])]
    public function put(Product $product, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('global', $request->headers->get('X-CSRF-TOKEN'))) {
            throw new AccessDeniedHttpException();
        }

        $cart = ($this->putProductInCart)($product);

        return new JsonResponse([
            'data' => $this->resource->normalize($cart),
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/{id<\d+>}', name: 'pick_up_product', methods: [Request::METHOD_DELETE])]
    public function delete(Product $product, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('global', $request->headers->get('X-CSRF-TOKEN'))) {
            throw new AccessDeniedHttpException();
        }

        $cart = ($this->pickUpProductFromCart)($product);

        return new JsonResponse([
            'data' => $this->resource->normalize($cart),
        ]);
    }
}
