<?php

namespace App\Common\Cart;

use App\Entity\Cart;
use App\Entity\Demand;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This implementation preserves the most valuable demands.
 */
class VenalMergingStrategy implements MergingStrategy
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(Cart ...$carts): Cart
    {
        $framework = $this->getFramework(...$carts);

        foreach ($carts as $cart) {
            foreach ($cart->getDemands() as $demand) /** @var Demand $demand */ {
                if ($framework->contains($demand)) {
                    $duplicated = $framework->getDemandOf($demand);

                    if ($duplicated->isLessValuableThan($demand)) {
                        $duplicated->setNumber($demand->getNumber());
                    }
                } else {
                    $framework->addDemand($demand);
                }
            }
        }

        return $framework;
    }

    /**
     * @param Cart ...$carts
     * @return Cart
     */
    protected function getFramework(Cart ...$carts): Cart
    {
        $preferred = new Cart();

        foreach ($carts as $cart) {
            if ($this->entityManager->contains($cart)) {
                return $cart;
            }
            $preferred = $cart;
        }

        return $preferred;
    }
}
