<?php

namespace App\Products\Infrastructure\Repository;

use App\Products\Domain\Entity\Product;
use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{

    private int $totalCount;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $product): void
    {
        $this->_em->persist($product);
        $this->_em->flush();
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

        $this->totalCount = count($result);

        if ($customFilter) {
            $result = array_slice($result, $offset, $limit);
        }

        return $result;
    }

    public function getFoundProductsCount(): int
    {
        return $this->totalCount ?? 0;
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
