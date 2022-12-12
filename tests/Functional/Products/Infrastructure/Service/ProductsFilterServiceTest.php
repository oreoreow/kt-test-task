<?php

namespace Functional\Products\Infrastructure\Service;

use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Factory\ProductFactory;
use App\Products\Domain\Factory\ProductFilterFactory;
use App\Products\Domain\Service\ProductsFilterServiceInterface;
use App\Products\Infrastructure\Repository\ProductRepository;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsFilterServiceTest extends WebTestCase
{
    private ProductsFilterServiceInterface $service;
    private ProductRepository $repository;
    private Generator $faker;

    public function setUp(): void
    {
        $this->service = static::getContainer()->get(ProductsFilterServiceInterface::class);
        $this->repository = static::getContainer()->get(ProductRepository::class);

        $this->faker = Factory::create();
        parent::setUp();
    }


    public function testProductFilteredSuccessfullyWithGWeight(): void
    {
        $name = $this->faker->name();
        $description = $this->faker->text();
        $weight = '1000000 g';
        $category = $this->faker->name();

        $product = (new ProductFactory())->create(
            $name,
            $description,
            $weight,
            $category
        );

        $this->repository->add($product);

        $productFilter = (new ProductFilterFactory())->create(
            ProductFilter::ORDER_BY_WEIGHT,
            ProductFilter::ORDER_ASC,
            10,
            1,
            $category,
            1000000,
            1000000
        );

        $existingProducts = $this->service->getFilteredProducts($productFilter);
        $existingProduct = $existingProducts[array_key_first($existingProducts)];

        $this->assertEquals($product->getName(), $existingProduct->getName());
    }

    public function testProductFilteredSuccessfullyWithKGWeight(): void
    {
        $name = $this->faker->name();
        $description = $this->faker->text();
        $weight = '10000 kg';
        $category = $this->faker->name();

        $product = (new ProductFactory())->create(
            $name,
            $description,
            $weight,
            $category
        );

        $this->repository->add($product);

        $productFilter = (new ProductFilterFactory())->create(
            ProductFilter::ORDER_BY_WEIGHT,
            ProductFilter::ORDER_ASC,
            10,
            1,
            $category,
            10000000,
            10000000
        );

        $existingProducts = $this->service->getFilteredProducts($productFilter);
        $existingProduct = $existingProducts[array_key_first($existingProducts)];

        $this->assertEquals($product->getName(), $existingProduct->getName());
    }

    public function testProductFilteredOrder(): void
    {
        $name = $this->faker->name();
        $description = $this->faker->text();
        $weight = '1 g';
        $category = $this->faker->name();

        $product = (new ProductFactory())->create(
            $name,
            $description,
            $weight,
            $category
        );
        $productFilter = (new ProductFilterFactory())->create(
            ProductFilter::ORDER_BY_WEIGHT,
            ProductFilter::ORDER_ASC,
            1000,
            1,
            null,
            1,
            1000000
        );
        $count = $this->service->getFilteredProductsCount($productFilter);

        $existingProductsOld = $this->service->getFilteredProducts($productFilter);
        $this->repository->add($product);

        $existingProducts = $this->service->getFilteredProducts($productFilter);

        $this->assertLessThan($count, 0);
        $this->assertLessThan(count($existingProducts), count($existingProductsOld));
    }
}