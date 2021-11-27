<?php

namespace App\UseCase\Product;

use App\Entity\Product;
use App\Exception\Product\ProductNotFoundException;
use App\Repository\ProductRepository;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Get the product by its id.
 */
final class GetProductById
{
    public const KEY = 'products:%d';
    public const TAGS = ['products'];

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
     * Get the product by its id.
     *
     * @param int $id the product id.
     * @throws ProductNotFoundException when cannot find the product using the provided id.
     */
    public function __invoke(int $id): Product
    {
        $key = $this->getKey($id);

        $get = fn() => $this->getProductById($id);

        try {
            return $this->cache->get($key, function (ItemInterface $item) use ($get) {
                $item->tag(self::TAGS);
                return $get();
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error('Bad cache arguments', ['exception' => $e, 'key' => $key]);
        }

        return $get();
    }

    private function getKey(int $id): string
    {
        return sprintf(
            self::KEY,
            $id,
        );
    }

    /**
     * @throws ProductNotFoundException
     */
    private function getProductById(int $id): Product
    {
        $product = $this->repository->findByIdJoinImagesJoinCategory($id);

        if (null === $product) {
            throw new ProductNotFoundException();
        }

        return $product;
    }
}
