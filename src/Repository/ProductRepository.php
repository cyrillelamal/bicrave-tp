<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Get the newest products with their images and categories.
     *
     * @param int $limit
     * @return Product[]
     */
    public function getNovelties(int $limit = 8): array
    {
        $qb = $this->getQueryBuilderJoinImagesJoinCategory()
            ->orderBy('product.createdAt', 'DESC')
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $this->fetchJoinCollection($query);
    }

    /**
     * Get the most popular products with their images and categories.
     *
     * @param int $limit
     * @return Product[]
     */
    public function getPopularProducts(int $limit = 8): array
    {
        $qb = $this->getQueryBuilderJoinImagesJoinCategory()
            ->orderBy('product.popularity', 'DESC')
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        return $this->fetchJoinCollection($query);
    }

    /**
     * Get pagination target for products that belong to the provided category.
     *
     * @param Category|int $category the category or its id.
     * @return Query there are joins on images and the category.
     */
    public function getQueryForCategoryPagination(Category|int $category): Query
    {
        $categoryId = $category instanceof Category ? $category->getId() : $category;

        $qb = $this->getQueryBuilderJoinImagesJoinCategory()
            ->where('category.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('product.createdAt', 'DESC');

        return $qb->getQuery();
    }

    /**
     * Find product by its id joining the related images and category.
     *
     * @param int $id
     * @return Product|null
     */
    public function findByIdJoinImagesJoinCategory(int $id): ?Product
    {
        $qb = $this->createQueryBuilder('product')
            ->leftJoin('product.images', 'images')
            ->addSelect('images')
            ->leftJoin('product.category', 'category')
            ->addSelect('category')
            ->where('product.id = :id')
            ->setParameter('id', $id);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $this->logger->critical('Bad schema: duplicated primary key', ['exception' => $e, 'id' => $id]);
            throw new LogicException('Multiple product definitions for the same primary key');
        }
    }

    /**
     * Find multiple products by their ids.
     * This method is N+1 dangerous since images aren't fetched.
     *
     * @param int ...$ids the product ids.
     * @return Product[]
     */
    public function findWhereIdIn(int ...$ids): array
    {
        return $this->createQueryBuilder('product')
            ->where('product.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    protected function getQueryBuilderJoinImagesJoinCategory(): QueryBuilder
    {
        return $this->createQueryBuilder('product')
            ->leftJoin('product.images', 'images')
            ->addSelect('images')
            ->leftJoin('product.category', 'category')
            ->addSelect('category');
    }

    /**
     * @return Product[]
     */
    protected function fetchJoinCollection(Query $query): array
    {
        try {
            $paginator = new Paginator($query, true);
            return iterator_to_array($paginator->getIterator());
        } catch (Exception $e) {
            $this->logger->error('Paginator failed', ['exception' => $e]);
            throw new RuntimeException('Paginator failed', previous: $e);
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
