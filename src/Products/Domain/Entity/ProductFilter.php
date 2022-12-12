<?php

namespace App\Products\Domain\Entity;

use JetBrains\PhpStorm\Pure;

class ProductFilter
{
    const ORDER_BY_NAME = 'name';
    const ORDER_BY_DESCRIPTION = 'description';
    const ORDER_BY_WEIGHT = 'weight';
    const ORDER_BY_CATEGORY = 'category';
    const VALID_ORDER_BY = [
        self::ORDER_BY_NAME,
        self::ORDER_BY_DESCRIPTION,
        self::ORDER_BY_WEIGHT,
        self::ORDER_BY_CATEGORY,
    ];
    const DEFAULT_ORDER_BY = self::ORDER_BY_NAME;

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';
    const VALID_ORDER = [
        self::ORDER_ASC,
        self::ORDER_DESC,
    ];
    const DEFAULT_ORDER = self::ORDER_BY_NAME;

    private int $page = 1;
    private ?int $limit;
    private string $orderBy = self::DEFAULT_ORDER_BY;
    private string $order = self::ORDER_BY_NAME;
    private ?string $category;
    private int $minGWeight = 0;
    private int $maxGWeight = 0;

    public function __construct()
    {

    }

    /**
     * @param int $page
     *
     * @return ProductFilter
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @param ?int $limit
     *
     * @return ProductFilter
     */
    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param string $orderBy
     *
     * @return ProductFilter
     */
    public function setOrderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param string|null $category
     *
     * @return ProductFilter
     */
    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param int $minGWeight
     *
     * @return ProductFilter
     */
    public function setMinGWeight(int $minGWeight): self
    {
        $this->minGWeight = $minGWeight;
        
        return $this;
    }

    /**
     * @param int $maxGWeight
     *
     * @return ProductFilter
     */
    public function setMaxGWeight(int $maxGWeight): self
    {
        $this->maxGWeight = $maxGWeight;
        
        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return ?int
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        $result = ($this->getPage() - 1) * $this->getLimit();
        return max($result, 0);
    }

    /**
     * @return ?string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @return int
     */
    public function getMinGWeight(): int
    {
        return $this->minGWeight;
    }

    /**
     * @return int
     */
    public function getMaxGWeight(): int
    {
        return $this->maxGWeight;
    }


}