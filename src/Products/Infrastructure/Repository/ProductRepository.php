<?php

namespace App\Products\Infrastructure\Repository;

use App\Products\Domain\Entity\Product;
use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function importProductsFromCSV(string $filename): bool
    {
        $sql = "LOAD DATA LOCAL INFILE '$filename'
                INTO TABLE products_product
                FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                IGNORE 0 ROWS (name, description, weight, category)";

        try {
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->executeQuery();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function add(Product $product): void
    {
        $this->_em->persist($product);
        $this->_em->flush();
    }


    /**
     * @param Product[] $products
     *
     * @return int
     */
    public function multiAdd(array $products): int
    {
        var_dump(count($products));die();
        // $savedProducts = 0;
        // $existingNames = $this->getCurrentNames();
        // foreach ($products as $product) {
        //     if(in_array($product->getName(), $existingNames)) continue;
        //
        //     $this->_em->persist($product);
        //     $this->_em->flush();
        //
        //     $savedProducts++;
        // }

        return 100;
    }

    /**
     * @return string[]
     */
    private function getCurrentNames():array
    {
        return array_map(function ($product){
            return $product['name'];
        }, $this->_em->createQueryBuilder()
            ->select('product.name')
            ->from(Product::class, 'product')
            ->getQuery()
            ->getResult());
    }


    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $customFilter = $orderBy &&
                        array_key_first($orderBy) == ProductFilter::ORDER_BY_WEIGHT
                        || array_key_exists('minWeight', $criteria)
                        || array_key_exists('maxWeight', $criteria);
        $finalLimit = $customFilter ? null : $limit;
        $finalOffset = $customFilter ? null : $offset;

        $minWeight = array_key_exists('minWeight', $criteria) ? $criteria['minWeight'] : 0;
        $maxWeight = array_key_exists('maxWeight', $criteria) ? $criteria['maxWeight'] : 0;


        unset($criteria['minWeight']);
        unset($criteria['maxWeight']);
        $result = parent::findBy($criteria, $orderBy, $finalLimit, $finalOffset);

        if ($minWeight) {
            $result = array_filter($result, function (Product $product)use($minWeight){
                return intval($product->getGWeight()) >= $minWeight;
            });
        }

        if ($maxWeight) {
            $result = array_filter($result, function (Product $product)use($maxWeight){
                return intval($product->getGWeight()) <= $maxWeight;
            });
        }


        if($orderBy && array_key_first($orderBy) == ProductFilter::ORDER_BY_WEIGHT) {

            $order = $orderBy[array_key_first($orderBy)];
            usort($result,
                function (Product $product1, Product $product2) use ($order) {
                    return $order == ProductFilter::ORDER_ASC ?
                        (intval($product1->getGWeight()) < intval($product2->getGWeight()) ? -1 : 1) :
                        (intval($product1->getGWeight()) > intval($product2->getGWeight()) ? -1 : 1);
                }
            );
        }

        if ($customFilter) {
            $result = array_slice($result, $offset, $limit);
        }

        return $result;
    }


    public function getCategories(): array
    {
        return array_map(function (Product $p) {
                return $p->getCategory();
            },
            $this->_em->createQueryBuilder()
                         ->select('product')
                         ->from(Product::class, 'product')
                         ->groupBy('product.category')
                         ->getQuery()
                         ->getResult()
        );
    }
}
