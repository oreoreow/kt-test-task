<?php

namespace App\Products\Domain\Repository;

use App\Products\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    public function add(Product $product): void;
    public function findById(int $id): Product;
}