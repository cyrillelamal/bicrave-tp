<?php

namespace App\UseCase\Product;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Get paginated category products.
 */
final class PaginateCategoryProducts
{
    public const KEY = 'categories:%d:page:%d:sort:%s:direction:%s';
    public const TAGS = ['products', 'categories'];

    public const LIMIT = 16;

    private LoggerInterface $logger;
    private PaginatorInterface $paginator;
    private ProductRepository $repository;
    private RequestStack $requestStack;
    private TagAwareCacheInterface $cache;

    public function __construct(
        LoggerInterface        $logger,
        PaginatorInterface     $paginator,
        ProductRepository      $repository,
        RequestStack           $requestStack,
        TagAwareCacheInterface $cache,
    )
    {
        $this->logger = $logger;
        $this->paginator = $paginator;
        $this->repository = $repository;
        $this->requestStack = $requestStack;
        $this->cache = $cache;
    }

    /**
     * Get paginated category products.
     *
     * @param Category|int $category the category or its id.
     * @return PaginationInterface
     */
    public function __invoke(Category|int $category): PaginationInterface
    {
        $key = $this->getKey($category);

        $paginate = fn() => $this->paginate($category);

        try {
            return $this->cache->get($key, function (ItemInterface $item) use ($paginate) {
                $item->tag(self::TAGS);
                return $paginate();
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error('Bad cache arguments', ['exception' => $e, 'key' => $key]);
        }

        return $paginate();
    }

    private function getKey(Category|int $category): string
    {
        $id = $category instanceof Category ? $category->getId() : $category;

        return sprintf(
            self::KEY,
            $id,
            $this->getPage(),
            $this->getSort(),
            $this->getDirection(),
        );
    }

    private function paginate(Category|int $category): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->getTarget($category),
            $this->getPage(),
            self::LIMIT,
        );
    }

    private function getTarget(Category|int $category): Query
    {
        return $this->repository->getQueryForCategoryPagination($category);
    }

    private function getPage(): int
    {
        return (int)$this->getRequest()->query->get('page', 1);
    }

    private function getSort(): string
    {
        return (string)$this->getRequest()->query->get('sort', '');
    }

    private function getDirection(): string
    {
        return (string)$this->getRequest()->query->get('direction', '');
    }

    private function getRequest(): Request
    {
        return $this->requestStack->getMainRequest() ?? Request::createFromGlobals();
    }
}
