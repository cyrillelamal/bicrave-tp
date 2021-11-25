<?php

namespace App\Message\Product;

use JetBrains\PhpStorm\Pure;

final class PaginateCategoryProductsMessage
{
    private int $categoryId;

    #[Pure] public function __construct(int $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    #[Pure] public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}
