<?php

namespace App\Products\Domain\Repository;

use App\Products\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function add(Product $product): void;
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array;
    public function getCategories(): array;
    public function importProductsFromCSV(string $filename);
}