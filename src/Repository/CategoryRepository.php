<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WeakMap;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Get all categories as trees of subcategories.
     *
     * @return Category[] top level categories with their entire trees.
     */
    public function getCategoryTrees(): array
    {
        $qb = $this->createQueryBuilder('category')
            ->leftJoin('category.parent', 'parent')
            ->orderBy('category.parent', 'DESC');

        return $this->buildCategoryTrees(...$qb->getQuery()->getResult());
    }

    /**
     * Build category trees.
     *
     * @param Category ...$categories parents must be first.
     * @return Category[] top level categories.
     */
    protected function buildCategoryTrees(Category ...$categories): array
    {
        $map = new WeakMap(); // parent -> children
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
                $child->getChildren()->clear(); // We must clear
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
