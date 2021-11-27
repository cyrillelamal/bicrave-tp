<?php

namespace App\Common\Cart;

use App\Entity\Cart;
use App\Entity\Demand;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartManager implements CartManagerInterface
{
    public const KEY = 'cart';

    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;
    private ProductRepository $productRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestStack           $requestStack,
        ProductRepository      $productRepository,
    )
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function save(Cart $cart): void
    {
        if ($this->entityManager->contains($cart)) {
            $this->persistOrRemoveDemands(...$cart->getDemands()->toArray());
            $this->entityManager->flush();
        } else {
            $cart->filterUselessDemands();
            $this->persistInSession($cart);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clear(Cart $cart): void
    {
        if ($this->entityManager->contains($cart)) {
            foreach ($cart->getDemands() as $demand) {
                $this->entityManager->remove($demand);
            }
            $this->entityManager->flush();
        } else {
            $cart->getDemands()->clear();
        }
    }

    protected function persistOrRemoveDemands(Demand ...$demands): void
    {
        // We must rehydrate all relations because the entity manager manages entities by references.
        // If we clone a product (this is probable since the cart may be stored in the session),
        // the entity manager will insert this product again while saving.
        $this->rehydrate(...$demands);

        foreach ($demands as $demand) {
            if ($demand->isUseless()) {
                $this->entityManager->remove($demand);
            } else {
                $this->entityManager->persist($demand);
            }
        }
    }

    protected function rehydrate(Demand ...$demands): void
    {
        $ids = array_map(fn(Demand $demand) => $demand->getProduct()->getId(), $demands);

        $products = $this->productRepository->findWhereIdIn(...$ids);

        $index = []; // product_id -> Demand
        foreach ($demands as $demand) {
            $index[$demand->getProduct()->getId()] = $demand;
        }

        foreach ($products as $product) {
            if (array_key_exists($product->getId(), $index)) {
                $index[$product->getId()]->setProduct($product);
            }
        }
    }

    protected function persistInSession(Cart $cart): void
    {
        $this->getSession()->set(self::KEY, $cart);
    }

    protected function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
