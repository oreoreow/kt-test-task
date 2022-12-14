<?php

namespace App\Products\Domain\Entity;

class ImportedProductsReport
{
    private int $requestedProductsCount;
    private int $savedProductsCount;
    private int $invalidLines;

    /**
     * @param int $savedProductsCount
     *
     * @return ImportedProductsReport
     */
    public function setSavedProductsCount(int $savedProductsCount): ImportedProductsReport
    {
        $this->savedProductsCount = $savedProductsCount;

        return $this;
    }

    /**
     * @param int $requestedProductsCount
     *
     * @return ImportedProductsReport
     */
    public function setRequestedProductsCount(int $requestedProductsCount): ImportedProductsReport
    {
        $this->requestedProductsCount = $requestedProductsCount;

        return $this;
    }

    /**
     * @param int $invalidLines
     *
     * @return ImportedProductsReport
     */
    public function setInvalidLines(int $invalidLines): ImportedProductsReport
    {
        $this->invalidLines = $invalidLines;

        return $this;
    }

    /**
     * @return int
     */
    public function getSavedProductsCount(): int
    {
        return $this->savedProductsCount;
    }

    /**
     * @return int
     */
    public function getRequestedProductsCount(): int
    {
        return $this->requestedProductsCount;
    }

    /**
     * @return int
     */
    public function getInvalidLines(): int
    {
        return $this->invalidLines;
    }

}