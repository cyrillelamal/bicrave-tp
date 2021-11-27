<?php

namespace App\Common\Cart;

use App\Entity\Cart;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionCartProvider implements CartProviderInterface
{
    public const KEY = 'cart';

    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,
    )
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function canProvideCart(): bool
    {
        return $this->getSession()->has(self::KEY);
    }

    /**
     * {@inheritDoc}
     */
    public function getCart(): Cart
    {
        return $this->getSession()->get(self::KEY, new Cart());
    }

    protected function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
