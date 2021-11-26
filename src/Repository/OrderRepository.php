<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Get pagination target for orders that belongs to the provided user.
     *
     * @param UserInterface|string $user the user or his/her email.
     * @return Query there are joins on reservations and the user.
     */
    public function getQueryForProfilePagination(UserInterface|string $user): Query
    {
        $email = $user instanceof UserInterface ? $user->getUserIdentifier() : $user;

        return $this->createQueryBuilder('o')
            ->join('o.customer', 'customer')
            ->addSelect('customer')
            ->leftJoin('o.reservations', 'reservations')
            ->addSelect('reservations')
            ->where('customer.email = :email')
            ->setParameter('email', $email)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery();
    }
}
