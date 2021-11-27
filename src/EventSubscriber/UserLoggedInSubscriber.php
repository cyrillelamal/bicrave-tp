<?php

namespace App\EventSubscriber;

use App\Common\Cart\CartManagerInterface;
use App\Common\Cart\MergingStrategy;
use App\Common\Cart\SessionCartProvider;
use App\Common\Cart\UserCartProvider;
use App\Entity\Cart;
use App\Event\UserLoggedInEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserLoggedInSubscriber implements EventSubscriberInterface
{
    private UserCartProvider $userCartProvider;
    private SessionCartProvider $sessionCartProvider;
    private MergingStrategy $mergingStrategy;
    private CartManagerInterface $cartManager;

    public function __construct(
        UserCartProvider     $userCartProvider,
        SessionCartProvider  $sessionCartProvider,
        MergingStrategy      $mergingStrategy,
        CartManagerInterface $cartManager,
    )
    {
        $this->userCartProvider = $userCartProvider;
        $this->sessionCartProvider = $sessionCartProvider;
        $this->mergingStrategy = $mergingStrategy;
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserLoggedInEvent::NAME => 'mergeCarts',
        ];
    }

    public function mergeCarts(): void
    {
        $cart = $this->mergingStrategy->merge(...$this->getCarts());

        $this->cartManager->save($cart);
    }

    /**
     * @return Cart[]
     */
    protected function getCarts(): array
    {
        return [
            $this->userCartProvider->getCart(),
            $this->sessionCartProvider->getCart(),
        ];
    }
}