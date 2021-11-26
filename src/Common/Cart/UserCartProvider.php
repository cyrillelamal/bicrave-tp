<?php

namespace App\Common\Cart;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Security\Role;
use Symfony\Component\Security\Core\Security;

class UserCartProvider implements CartProviderInterface
{
    private Security $security;
    private CartRepository $repository;

    public function __construct(
        Security       $security,
        CartRepository $repository,
    )
    {
        $this->security = $security;
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function canProvideCart(): bool
    {
        return $this->security->isGranted(Role::CUSTOMER);
    }

    /**
     * {@inheritDoc}
     */
    public function getCart(): Cart
    {
        return $this->repository->findByUserJoinDemands($this->security->getUser()) ?? new Cart();
    }
}
