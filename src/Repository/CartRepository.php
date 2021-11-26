<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * @param UserInterface|string $user
     * @return Cart|null
     */
    public function findByUserJoinDemands(UserInterface|string $user): ?Cart
    {
        $identifier = $user instanceof UserInterface ? $user->getUserIdentifier() : $user;

        $qb = $this->createQueryBuilder('cart')
            ->leftJoin('cart.demands', 'demands')
            ->addSelect('demands')
            ->leftJoin('demands.product', 'product')
            ->addSelect('product')
            ->leftJoin('product.images', 'images')
            ->addSelect('images')
            ->join('cart.owner', 'owner')
            ->where('owner.email = :identifier')
            ->addSelect('owner')
            ->setParameter('identifier', $identifier);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $this->logger->error('Multiple shopping cart definitions', ['exception' => $e, 'user' => $user]);
            throw new RuntimeException('Multiple shopping cart definitions', previous: $e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
