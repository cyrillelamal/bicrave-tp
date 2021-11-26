<?php

namespace App\Common\Cart;

use App\Entity\Cart;
use JetBrains\PhpStorm\Pure;

class ChainCartProvider implements CartProviderInterface
{
    private SessionCartProvider $sessionCartProvider;
    private UserCartProvider $userCartProvider;

    private ?Cart $cart = null;

    public function __construct(
        SessionCartProvider $sessionCartProvider,
        UserCartProvider    $userCartProvider,
    )
    {
        $this->sessionCartProvider = $sessionCartProvider;
        $this->userCartProvider = $userCartProvider;
    }

    /**
     * {@inheritDoc}
     */
    #[Pure] public function canProvideCart(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getCart(): Cart
    {
        if (null !== $this->cart) {
            return $this->cart;
        }

        foreach ($this->getProviders() as $provider) {
            if ($provider->canProvideCart()) {
                return $this->cart = $provider->getCart();
            }
        }

        return $this->cart = new Cart();
    }

    /**
     * @return CartProviderInterface[]
     */
    #[Pure] protected function getProviders(): array
    {
        return [
            $this->sessionCartProvider,
            $this->userCartProvider,
        ];
    }
}