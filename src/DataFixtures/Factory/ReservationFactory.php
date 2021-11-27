<?php

namespace App\DataFixtures\Factory;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Reservation;

class ReservationFactory
{
    /**
     * Simple factory for reservation entities.
     *
     * @param Product $product the reserved product.
     * @param Order $order the related order.
     * @return Reservation
     */
    public static function makeReservation(Product $product, Order $order): Reservation
    {
        $reservation = new Reservation();

        $reservation->setProduct($product);
        $reservation->setOrder($order);
        $reservation->setNumber(rand(1, 9));
        $reservation->setCost(rand(99, 99_99));

        return $reservation;
    }
}
