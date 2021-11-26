<?php

namespace App\UseCase\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use WeakMap;

/**
 * Get the top level categories with their entire subcategory trees.
 */
final class GetCategoryTrees
{
    public const KEY = 'categories:trees';
    public const TAGS = ['categories'];

    private LoggerInterface $logger;
    private CategoryRepository $repository;
    private TagAwareCacheInterface $cache;

    public function __construct(
        LoggerInterface        $logger,
        CategoryRepository     $repository,
        TagAwareCacheInterface $cache,
    )
    {
        $this->logger = $logger;
        $this->repository = $repository;
        $this->cache = $cache;
    }

    /**
     * Get the top level categories with their entire subcategory trees.
     *
     * @return Category[]
     */
    public function __invoke(): array
    {
        try {
            return $this->cache->get(self::KEY, function (ItemInterface $item) {
                $item->tag(self::TAGS);
                return $this->getCategoryTrees();
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error('Bad cache arguments', ['exception' => $e, 'key' => self::KEY]);
        }

        return $this->getCategoryTrees();
    }

    /**
     * @return Category[]
     */
    private function getCategoryTrees(): array
    {
        $categories = $this->repository->findAllJoinParentOrderByParentDesc();

        return $this->hydrate(...$categories);
    }

    /**
     * @return Category[]
     */
    private function hydrate(Category ...$categories): array
    {
        $map = new WeakMap(); // parent -> children  # flat
        $top = [];

        foreach ($categories as $category) {
            if ($category->hasParent()) {
                $parent = $category->getParent();
                $children = $map->offsetExists($parent) ? [...$map->offsetGet($parent), $category] : [$category];
                $map->offsetSet($parent, $children);
            } else {
                $top[] = $category;
            }
        }

        foreach ($map->getIterator() as $children) {
            foreach ($children as $child) /** @var Category $child */ {
                $child->getChildren()->clear();
            }
        }

        foreach ($map->getIterator() as $parent => $children) /** @var Category $parent */ {
            $parent->getChildren()->clear();
            foreach ($children as $child) /** @var Category $child */ {
                $parent->addChild($child);
            }
        }

        return $top;
    }
}
