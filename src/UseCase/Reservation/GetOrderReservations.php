<?php

namespace App\UseCase\Reservation;

use App\Entity\Order;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;

/**
 * Get all order reservations.
 *
 */
class GetOrderReservations
{
    private ReservationRepository $repository;

    public function __construct(
        ReservationRepository $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * Get all order reservations.
     *
     * @param Order $order
     * @return Reservation[]
     */
    public function __invoke(Order $order): array
    {
        return $this->repository->findByOrderJoinProducts($order);
    }
}
