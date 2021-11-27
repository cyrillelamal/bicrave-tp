<?php

namespace App\Tests\Entity;

use App\Entity\Demand;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class DemandTest extends TestCase
{
    /**
     * @test
     */
    public function it_makes_a_demand_of_a_product(): void
    {
        $product = new Product();

        $demand = Demand::of($product);

        $this->assertSame($product, $demand->getProduct());
    }

    /**
     * @test
     */
    public function it_checks_if_it_represents_a_product(): void
    {
        $product = new Product();

        $demand = Demand::of($product);

        $this->assertTrue($demand->representsProduct($product));
    }

    /**
     * @test
     */
    public function it_adds_more_items(): void
    {
        $demand = new Demand();

        $before = $demand->getNumber();

        $number = rand(1, 99);

        $demand->more($number);

        $this->assertEquals($before + $number, $demand->getNumber());
    }

    /**
     * @test
     */
    public function it_picks_up_items(): void
    {
        $demand = new Demand();

        $before = rand(19, 99);
        $number = rand(1, 9);

        $demand->setNumber($before);

        $demand->less($number);

        $this->assertEquals($before - $number, $demand->getNumber());
    }

    /**
     * @test
     */
    public function it_checks_if_the_demand_is_useless(): void
    {
        $useless = new Demand();
        $useless->setNumber(0);

        $this->assertTrue($useless->isUseless());
    }

    /**
     * @test
     */
    public function it_chooses_the_most_valuable_demand(): void
    {
        $product = new Product();
        $product->setCost(1);
        $basic = Demand::of($product, 1);
        $valuable = Demand::of($product, 9);

        $this->assertTrue($basic->isLessValuableThan($valuable));
        $this->assertFalse($valuable->isLessValuableThan($basic));
    }
}
