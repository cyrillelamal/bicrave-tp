<?php

namespace App\UseCase\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;

/**
 * Update the children of the provided category by making them its siblings.
 */
class MoveUpChildren
{
    private CategoryRepository $repository;

    public function __construct(
        CategoryRepository $repository,
    )
    {
        $this->repository = $repository;
    }

    /**
     * Update the children of the provided category by making them its siblings.
     *
     * @param Category $category the parent category.
     */
    public function __invoke(Category $category): void
    {
        $parent = $category->getParent();
        $children = $this->repository->findChildren($category);

        foreach ($children as $child) {
            $child->setParent($parent);
        }
    }
}
