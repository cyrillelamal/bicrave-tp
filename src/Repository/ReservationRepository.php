<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Get all order reservations.
     *
     * @param Order|int $order the order or its id.
     * @return Reservation[]
     */
    public function findByOrderJoinProducts(Order|int $order): array
    {
        $id = $order instanceof Order ? $order->getId() : $order;

        return $this->createQueryBuilder('r')
            ->join('r.order', 'o')
            ->addSelect('o')
            ->join('r.product', 'p')
            ->addSelect('p')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
