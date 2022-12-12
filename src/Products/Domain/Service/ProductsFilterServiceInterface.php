<?php

namespace App\Products\Domain\Service;

use App\Products\Domain\Entity\ProductFilter;

interface ProductsFilterServiceInterface
{
    public function getFilteredProducts(ProductFilter $productFilter): array;
    public function getFilteredProductsCount(ProductFilter $productFilter): int;
}