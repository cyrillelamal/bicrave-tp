<?php

namespace App\DataFixtures\Factory;

use App\Common\Order\Status;
use App\Entity\Order;
use App\Entity\User;

class OrderFactory
{
    use Faker;

    /**
     * Simple factory for order entities.
     *
     * @param User $customer the user that performed the orders.
     * @param int $count how many orders to create.
     * @return Order[]
     */
    public static function makeOrders(User $customer, int $count = 1): array
    {
        return array_map(function () use ($customer) {
            $order = new Order();

            $order->setCreatedAt(self::getFaker()->dateTimeBetween('-2 years'));
            $order->setUpdatedAt(self::getFaker()->dateTimeBetween($order->getCreatedAt()->format(DATE_ATOM)));
            $order->setStatus(Status::CREATED);
            $order->setCustomer($customer);

            return $order;
        }, array_fill(0, $count, null));
    }
}