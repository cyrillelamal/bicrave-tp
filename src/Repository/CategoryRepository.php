<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findAllJoinParentOrderByParentDesc(): array
    {
        return $this->createQueryBuilder('category')
            ->leftJoin('category.parent', 'parent')
            ->orderBy('category.parent', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find children by the parent.
     *
     * @param Category|int $parent the parent category or its id.
     * @return Category[]
     */
    public function findChildren(Category|int $parent): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.parent', 'parent')
            ->addSelect('parent')
            ->where('c.parent = :parent')
            ->setParameter('parent', $parent)
            ->getQuery()
            ->getResult();
    }
}
