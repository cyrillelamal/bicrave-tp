<?php

namespace App\Controller;

use App\Common\Cart\CartResource;
use App\Entity\Product;
use App\UseCase\Cart\PickUpProductFromCart;
use App\UseCase\Cart\PutProductInCart;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

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
        return $this->render('cart/index.html.twig');
    }

    /**
     * @OA\Patch(
     *     path="/carts/{productId}",
     *     summary="Put the product in the shopping cart",
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The shopping cart content",
     *         @OA\JsonContent(type="object", ref="#/components/schemas/Cart"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="The product was not found",
     *     ),
     * )
     * @throws Throwable -> 500
     */
    #[Route(path: '/{id<\d+>}', name: 'put_product', methods: [Request::METHOD_PATCH, Request::METHOD_PUT])]
    public function demand(Product $product, Request $request): Response
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
     * @OA\Delete(
     *     path="/carts/{productId}",
     *     summary="Pick up the product from the shopping cart",
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The shopping cart content",
     *         @OA\JsonContent(type="object", ref="#/components/schemas/Cart"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="The product was not found",
     *     ),
     * )
     * @throws Throwable -> 500
     */
    #[Route(path: '/{id<\d+>}', name: 'pick_up_product', methods: [Request::METHOD_DELETE])]
    public function remove(Product $product, Request $request): Response
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
