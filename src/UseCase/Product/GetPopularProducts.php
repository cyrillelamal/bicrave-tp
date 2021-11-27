<?php

namespace App\UseCase\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Get the most popular products.
 */
final class GetPopularProducts
{
    public const KEY = 'products:popular';
    public const TAGS = ['products'];

    public const LIMIT = 8;

    private LoggerInterface $logger;
    private ProductRepository $repository;
    private TagAwareCacheInterface $cache;

    public function __construct(
        LoggerInterface        $logger,
        ProductRepository      $repository,
        TagAwareCacheInterface $cache,
    )
    {
        $this->logger = $logger;
        $this->repository = $repository;
        $this->cache = $cache;
    }

    /**
     * Get the most popular products.
     *
     * @return Product[]
     */
    public function __invoke(): array
    {
        try {
            return $this->cache->get(self::KEY, function (ItemInterface $item) {
                $item->tag(self::TAGS);
                return $this->getPopularProducts();
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error('Bad cache arguments', ['exception' => $e, 'key' => self::KEY]);
        }

        return $this->getPopularProducts();
    }

    /**
     * @return Product[]
     */
    private function getPopularProducts(): array
    {
        return $this->repository->getPopularProducts(self::LIMIT);
    }
}
