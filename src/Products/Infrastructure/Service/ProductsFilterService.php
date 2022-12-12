<?php

namespace App\Products\Infrastructure\Service;


use App\Products\Domain\Entity\Product;
use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Repository\ProductRepositoryInterface;
use App\Products\Domain\Service\ProductsFilterServiceInterface;

class ProductsFilterService implements ProductsFilterServiceInterface
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function getFilteredProducts(
        ProductFilter $productFilter
    ): array
    {
        $criteria = [];
        if (in_array($productFilter->getCategory(), $this->getCategories())) {
            $criteria = ['category' => $productFilter->getCategory()];
        }

        if($productFilter->getMinGWeight()) {
            $criteria = array_merge($criteria, ['minWeight' => $productFilter->getMinGWeight()]);
        }

        if($productFilter->getMaxGWeight()) {
            $criteria = array_merge($criteria, ['maxWeight' => $productFilter->getMaxGWeight()]);
        }

        return $this->productRepository->findBy($criteria, [$productFilter->getOrderBy() => $productFilter->getOrder()], $productFilter->getLimit(), $productFilter->getOffset());
    }

    public function getCategories(): array
    {
        return $this->productRepository->getCategories();
    }

    public function getFilteredProductsCount(ProductFilter $productFilter): int
    {
        if($this->productRepository->getFoundProductsCount() == 0) {
            $this->getFilteredProducts($productFilter);
        }

        return $this->productRepository->getFoundProductsCount();
    }
}