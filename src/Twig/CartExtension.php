<?php

namespace App\Twig;

use App\Common\Cart\CartProviderInterface;
use App\Entity\Cart;
use App\Entity\Demand;
use App\Entity\Product;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CartExtension extends AbstractExtension
{
    private CartProviderInterface $provider;

    /**
     * Cached.
     */
    private Cart $cart;

    public function __construct(
        CartProviderInterface $provider,
    )
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('demand', fn(Product $product) => $this->getDemandByProduct($product)),
        ];
    }

    protected function getDemandByProduct(Product $product): Demand
    {
        return $this->getCart()->getDemandOf($product);
    }

    private function getCart(): Cart
    {
        return $this->cart ?? $this->cart = $this->provider->getCart();
    }
}