<?php

namespace App\Repository;

use App\Entity\Product;
use App\Repository\Common\QueryMutatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
     * @param QueryMutatorInterface|null $mutator
     * @return Product[]
     */
    public function getNovelties(?QueryMutatorInterface $mutator = null): array
    {
        $qb = $this->getQueryBuilderJoinImagesJoinCategory()->orderBy('product.createdAt', 'DESC');

        $mutator?->mutateQueryBuilder($qb);

        $query = $qb->getQuery();

        $mutator?->mutateQuery($query);

        return $this->fetchJoinCollection($query);
    }

    /**
     * Get the most popular products with their images and categories.
     *
     * @param QueryMutatorInterface|null $mutator
     * @return Product[]
     */
    public function getPopularProducts(?QueryMutatorInterface $mutator = null): array
    {
        $qb = $this->getQueryBuilderJoinImagesJoinCategory()->orderBy('product.popularity', 'DESC');

        $mutator?->mutateQueryBuilder($qb);

        $query = $qb->getQuery();

        $mutator?->mutateQuery($query);

        return $this->fetchJoinCollection($query);
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
