<?php

namespace App\MessageHandler\Category;

use App\Entity\Category;
use App\Message\Category\GetCategoryTreesMessage;
use App\Repository\CategoryRepository;
use App\Repository\Common\QueryMutatorInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GetCategoryTreesMessageHandler implements MessageHandlerInterface, QueryMutatorInterface
{
    public const LIFETIME = 365 * 24 * 3600;
    public const ID = 'categories:all';

    private CategoryRepository $repository;

    public function __construct(
        CategoryRepository $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * @return Category[]
     */
    public function __invoke(GetCategoryTreesMessage $message): array
    {
        return $this->repository->getCategoryTrees($this);
    }

    /**
     * {@inheritDoc}
     */
    public function mutateQueryBuilder(QueryBuilder $builder): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function mutateQuery(Query $query): void
    {
        $query->enableResultCache(self::LIFETIME, self::ID);
    }
}
