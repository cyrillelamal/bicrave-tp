<?php

namespace App\Exception\Product;

use App\Entity\Product;
use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class ProductsOutOfStockException extends Exception
{
    private array $products;

    /**
     * {@inheritDoc}
     * @param array $products the products that have caused the exception.
     */
    #[Pure] public function __construct(array $products = [], $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct();

        $this->products = $products;
    }

    /**
     * Get the products that are out of stock.
     *
     * @return Product[]
     */
    #[Pure] public function getProducts(): array
    {
        return $this->products;
    }
}
