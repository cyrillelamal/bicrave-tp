<?php

namespace App\Event;

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderCreatedEvent extends Event
{
    public const NAME = 'order.created';

    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the created order.
     */
    public function getOrder(): Order
    {
        return $this->order;
    }
}
