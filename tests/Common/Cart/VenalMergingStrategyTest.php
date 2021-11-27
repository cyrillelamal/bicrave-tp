<?php

namespace App\Tests\Common\Cart;

use App\Common\Cart\VenalMergingStrategy;
use App\Entity\Cart;
use App\Entity\Demand;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class VenalMergingStrategyTest extends TestCase
{
    private VenalMergingStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->strategy = new VenalMergingStrategy(
            $this->createMock(EntityManagerInterface::class),
        );
    }

    /**
     * @test
     */
    public function it_always_returns_a_cart(): void
    {
        $this->assertNotNull($this->strategy->merge());

        $this->assertNotNull($this->strategy->merge(new Cart()));
    }

    /**
     * @test
     */
    public function it_prefers_entities_as_framework(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('contains')->willReturn(true);

        $strategy = new VenalMergingStrategy($em);

        $cart = new Cart();

        $merged = $strategy->merge($cart);

        $this->assertSame($cart, $merged);
    }

    /**
     * @test
     */
    public function it_falls_back_to_the_last_cart(): void
    {
        $carts = [new Cart(), new Cart(), new Cart()];
        $expected = end($carts);

        $merged = $this->strategy->merge(...$carts);
        $this->assertSame($expected, $merged);
    }

    /**
     * @test
     */
    public function it_chooses_the_most_valuable_demands(): void
    {
        $carts = [
            new Cart(),
            new Cart(),
        ];

        $product = new Product();
        $product->setCost(100);
        $basic = Demand::of($product, 1);
        $valuable = Demand::of($product, 9);

        $carts[0]->addDemand($basic);
        $carts[1]->addDemand($valuable);

        $merged = $this->strategy->merge(...$carts);

        $this->assertCount(1, $merged->getDemands());

        $this->assertSame($valuable, $merged->getDemands()->first());
    }
}
