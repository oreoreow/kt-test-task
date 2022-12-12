<?php

namespace Functional\Products\Infrastructure\Repository;

use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Factory\ProductFactory;
use App\Products\Domain\Factory\ProductFilterFactory;
use App\Products\Domain\Service\ProductsFilterServiceInterface;
use App\Products\Infrastructure\Repository\ProductRepository;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductRepositoryTest extends WebTestCase
{
    private ProductRepository $repository;
    private Generator $faker;

    public function setUp(): void
    {
        $this->repository = static::getContainer()->get(ProductRepository::class);
        $this->faker = Factory::create();
        parent::setUp();
    }

    public function testProductAddedAndGotSuccessfully(): void
    {
        $name = $this->faker->name();
        $description = $this->faker->text();
        $weight = $this->faker->randomNumber(4) . " " . $this->faker->randomElement(['kg', 'g']);
        $category = $this->faker->name();

        $product = (new ProductFactory())->create(
            $name,
            $description,
            $weight,
            $category
        );

        $this->repository->add($product);

        $existingProducts = $this->repository->findBy(['id' => $product->getId()]);
        $existingProduct = $existingProducts[array_key_first($existingProducts)];

        $this->assertEquals($product->getId(), $existingProduct->getId());
        $this->assertEquals($product->getName(), $existingProduct->getName());
        $this->assertEquals($product->getDescription(), $existingProduct->getDescription());
        $this->assertEquals($product->getWeight(), $existingProduct->getWeight());
        $this->assertEquals($product->getCategory(), $existingProduct->getCategory());
    }
}