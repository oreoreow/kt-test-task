<?php

namespace App\Products\Domain\Factory;

use App\Products\Domain\Entity\ImportedProductsReport;

class ImportedProductsReportFactory
{
    public function create(
        int $requestedProductsCount,
        int $savedProductsCount,
        int $invalidLines
    ): ImportedProductsReport {

        return (new ImportedProductsReport())
            ->setRequestedProductsCount($requestedProductsCount)
            ->setSavedProductsCount($savedProductsCount)
            ->setInvalidLines($invalidLines);
    }
}