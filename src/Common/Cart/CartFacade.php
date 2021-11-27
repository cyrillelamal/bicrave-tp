<?php

namespace App\Common\Cart;

use App\Entity\Cart;
use App\Entity\Demand;
use Doctrine\Common\Collections\Collection;

/**
 * This class is intended for read-only use, for example, in templates.
 */
class CartFacade
{
    private CartProviderInterface $provider;

    /**
     * Cached
     */
    private ?Cart $cart = null;

    public function __construct(
        CartProviderInterface $provider,
    )
    {
        $this->provider = $provider;
    }

    /**
     * Get the shopping cart total cost.
     *
     * @return int cents.
     */
    public function getTotal(): int
    {
        return $this->getCart()->getTotal();
    }

    /**
     * Get the final shopping cart price.
     *
     * @return float not formatted application currency value.
     */
    public function getPrice(): float
    {
        return $this->getCart()->getPrice();
    }

    /**
     * Get the shopping cart demands.
     *
     * @return Collection<Demand>
     */
    public function getDemands(): Collection
    {
        return $this->getCart()->getDemands();
    }

    /**
     * Check if this shopping cart has no demanded products.
     */
    public function isEmpty(): bool
    {
        return $this->getCart()->isEmpty();
    }

    protected function getCart(): Cart
    {
        return $this->cart ?? $this->cart = $this->provider->getCart();
    }
}
