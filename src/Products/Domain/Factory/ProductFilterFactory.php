<?php

namespace App\Products\Domain\Factory;

use App\Products\Domain\Entity\ProductFilter;

class ProductFilterFactory
{
    public function create(
        string $orderBy,
        string $order,
        ?string $limit,
        string $page,
        ?string $category,
        string $minWeight,
        string $maxWeight,
    ): ProductFilter {
        return (new ProductFilter())
            ->setLimit($limit)
            ->setOrder($order)
            ->setOrderBy($orderBy)
            ->setPage($page)
            ->setCategory($category)
            ->setMinGWeight(intval($minWeight))
            ->setMaxGWeight(intval($maxWeight));
    }
}