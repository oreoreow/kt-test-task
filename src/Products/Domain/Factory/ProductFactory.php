<?php

namespace App\Products\Domain\Factory;

use App\Products\Domain\Entity\Product;

class ProductFactory
{
    public function create(
        string $name,
        string $description,
        string $weight,
        string $category
    ): Product {

        return (new Product())
            ->setName($name)
            ->setDescription($description)
            ->setWeight($weight)
            ->setCategory($category);
    }
}