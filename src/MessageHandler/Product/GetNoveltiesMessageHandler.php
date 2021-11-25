<?php

namespace App\MessageHandler\Product;

use App\Entity\Product;
use App\Message\Product\GetNoveltiesMessage;
use App\Repository\Common\QueryMutatorInterface;
use App\Repository\ProductRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GetNoveltiesMessageHandler implements MessageHandlerInterface, QueryMutatorInterface
{
    public const LIMIT = 8;
    public const LIFETIME = 365 * 24 * 3600;
    public const ID = 'products:novelties';

    private ProductRepository $repository;

    public function __construct(
        ProductRepository $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * @return Product[]
     */
    public function __invoke(GetNoveltiesMessage $message): array
    {
        return $this->repository->getNovelties($this);
    }

    /**
     * {@inheritDoc}
     */
    public function mutateQueryBuilder(QueryBuilder $builder): void
    {
        $builder->setMaxResults(self::LIMIT);
    }

    /**
     * {@inheritDoc}
     */
    public function mutateQuery(Query $query): void
    {
        $query->enableResultCache(self::LIFETIME, self::ID);
    }
}
