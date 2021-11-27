<?php

namespace App\Service\Payment;

use App\Entity\Order;
use App\Service\Payment\Exception\PaymentFailedException;

interface PaymentProcessorInterface
{
    /**
     * Perform a single payment.
     *
     * @param Order $order the paid order.
     * @throws PaymentFailedException when there are any problem with the implementing payment service.
     */
    public function charge(Order $order, array $params = []): void;
}
