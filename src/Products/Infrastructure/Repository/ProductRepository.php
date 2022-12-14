<?php

namespace App\Products\Infrastructure\Repository;

use App\Products\Domain\Entity\Product;
use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws Exception
     */
    public function importProductsFromCSV(string $filename): bool
    {
        $sql = "LOAD DATA LOCAL INFILE '$filename'
                INTO TABLE products_product
                FIELDS TERMINATED BY ',' ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                IGNORE 0 ROWS (name, description, @weight, category)
                SET weight = REPLACE(REPLACE(@weight, ' kg', '000'), ' g', '')";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->executeQuery();

        return true;
    }

    public function add(Product $product): void
    {
        $this->_em->persist($product);
        $this->_em->flush();
    }

    private function createCriteriaFromArray(array $criteria): Criteria
    {
        $minWeight = array_key_exists('minWeight', $criteria) ? $criteria['minWeight'] : 0;
        $maxWeight = array_key_exists('maxWeight', $criteria) ? $criteria['maxWeight'] : 0;

        $criteriaObj = new Criteria();

        if($minWeight) {
            $criteriaObj->andWhere(Criteria::expr()->gte('weight', $minWeight));
            unset($criteria['minWeight']);
        }

        if($maxWeight) {
            $criteriaObj->andWhere(Criteria::expr()->lte('weight', $maxWeight));
            unset($criteria['maxWeight']);
        }

        if(array_key_exists('category', $criteria)) {
            $criteriaObj->andWhere(Criteria::expr()->eq('category', $criteria['category']));
            unset($criteria['category']);
        }

        foreach($criteria as $field => $value) {
            $criteriaObj->andWhere(Criteria::expr()->eq($field, $value));
        }

        return $criteriaObj;
    }


    /**
     * @throws QueryException
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {

        $criteriaObj = $this->createCriteriaFromArray($criteria);

        $result = $this->_em->createQueryBuilder()
                            ->select('p')
                            ->from(Product::class, 'p')
                            ->addCriteria($criteriaObj)
                            ->setMaxResults($limit)
                            ->setFirstResult($offset);

        if($orderBy) $result->orderBy('p.'.array_key_first($orderBy), $orderBy[array_key_first($orderBy)]);

        return $result->getQuery()->getResult();
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

    /**
     * @throws QueryException|NoResultException|NonUniqueResultException
     */
    public function countBy(array $criteria, $limit = null, $offset = null): int
    {
        $criteriaObj = $this->createCriteriaFromArray($criteria);
        return $this->_em->createQueryBuilder()->select('count(p.id)')->from(Product::class, 'p')->addCriteria($criteriaObj)->getQuery()->getSingleScalarResult();
    }
}
