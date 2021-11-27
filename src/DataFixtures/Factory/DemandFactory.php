<?php

namespace App\DataFixtures\Factory;

use App\Entity\Cart;
use App\Entity\Demand;
use App\Entity\Product;

class DemandFactory
{
    use Faker;

    /**
     * Simple factory for demand entities.
     *
     * @param Product $product the related product.
     * @param Cart $cart the related shopping cart.
     * @return Demand
     */
    public static function makeDemand(Product $product, Cart $cart): Demand
    {
        $demand = new Demand();

        $demand->setProduct($product);
        $demand->setNumber(rand(1, 9));
        $demand->setCart($cart);
        $demand->setCreatedAt(self::getFaker()->dateTimeBetween($product->getCreatedAt()->format(DATE_ATOM)));

        return $demand;
    }
}
