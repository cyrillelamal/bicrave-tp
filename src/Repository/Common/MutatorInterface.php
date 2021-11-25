<?php

namespace App\Repository\Common;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * This interface is a part of the implementation of template method also knows as algorithm pattern.
 * It let you easily modify your DQL queries and query builders.
 *
 * Repositories are supposed to receive implementations and apply them.
 *
 * It seems more convenient to extract some parameters outside of repositories.
 * For example, the client code, e.g. a very simple CRUD controller, may use it to add pagination or cache.
 */
interface MutatorInterface
{
    /**
     * Apply some changes to the query builder.
     *
     * @param QueryBuilder $builder
     */
    public function mutateQueryBuilder(QueryBuilder $builder): void;

    /**
     * Apply some changes to the DQL query.
     *
     * @param Query $query
     */
    public function mutateQuery(Query $query): void;
}
