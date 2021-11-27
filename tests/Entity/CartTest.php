<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Entity\Demand;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides_a_new_demand_when_the_product_has_not_been_demanded(): void
    {
        $cart = new Cart();
        $product = new Product();

        $demand = $cart->getDemandOf($product);

        $this->assertNull($demand->getId());
        $this->assertSame($product, $demand->getProduct());
        $this->assertCount(0, $cart->getDemands());
    }

    /**
     * @test
     */
    public function it_provides_the_demand_that_represents_the_product(): void
    {
        $cart = new Cart();
        $product = new Product();
        $demand = new Demand();
        $demand->setProduct($product);
        $cart->getDemands()->add($demand);

        $this->assertSame($demand, $cart->getDemandOf($product));
    }

    /**
     * @test
     */
    public function it_checks_if_the_product_has_been_demanded(): void
    {
        $cart = new Cart();
        $product = new Product();

        $this->assertFalse($cart->contains($product));

        $demand = new Demand();
        $demand->setProduct($product);
        $cart->getDemands()->add($demand);

        $this->assertTrue($cart->contains($product));
    }

    /**
     * @test
     */
    public function it_adds_new_demands(): void
    {
        $cart = new Cart();
        $product = new Product();
        $demand = new Demand();
        $demand->setProduct($product);

        $before = $cart->getDemands()->count();

        $cart->addDemand($demand);

        $this->assertCount($before + 1, $cart->getDemands());
        $this->assertNotNull($demand->getCart());
    }

    /**
     * @test
     */
    public function it_removes_demands(): void
    {
        $cart = new Cart();
        $product = new Product();
        $demand = new Demand();
        $demand->setProduct($product);
        $cart->getDemands()->add($demand);

        $before = $cart->getDemands()->count();

        $cart->removeDemand($demand);

        $this->assertCount($before - 1, $cart->getDemands());
    }
}
