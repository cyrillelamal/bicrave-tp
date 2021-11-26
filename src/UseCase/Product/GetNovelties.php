<?php

namespace App\UseCase\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Get the newest products.
 */
final class GetNovelties
{
    public const KEY = 'products:novelties';
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
     * Get the newest products.
     *
     * @return Product[]
     */
    public function __invoke(): array
    {
        try {
            return $this->cache->get(self::KEY, function (ItemInterface $item) {
                $item->tag(self::TAGS);
                return $this->getNovelties();
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error('Bad cache arguments', ['exception' => $e, 'key' => self::KEY]);
        }

        return $this->getNovelties();
    }

    /**
     * @return Product[]
     */
    private function getNovelties(): array
    {
        return $this->repository->getNovelties(self::LIMIT);
    }
}
