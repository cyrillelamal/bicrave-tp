<?php

namespace App\UseCase\Cart;

use App\Common\Cart\CartManagerInterface;
use App\Common\Cart\CartProviderInterface;
use App\Entity\Cart;
use App\Entity\Product;
use Psr\Log\LoggerInterface;

/**
 * Put some amount of product in the shopping cart.
 */
final class PutProductInCart
{
    private LoggerInterface $logger;
    private CartProviderInterface $provider;
    private CartManagerInterface $manager;

    public function __construct(
        LoggerInterface       $logger,
        CartProviderInterface $provider,
        CartManagerInterface  $manager,
    )
    {
        $this->logger = $logger;
        $this->provider = $provider;
        $this->manager = $manager;
    }

    /**
     * Put some amount of product in the shopping cart.
     */
    public function __invoke(Product $product): Cart
    {
        $cart = $this->provider->getCart();

        $cart->demand($product);

        $this->logger->debug('Saving cart', ['manager' => $this->manager, 'cart' => $cart]);
        $this->manager->save($cart);

        return $cart;
    }
}
