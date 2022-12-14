<?php

namespace App\Products\Domain\Service;

use App\Products\Domain\Entity\ImportedProductsReport;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ProductsImportServiceInterface
{
    public function importProductsFromXML(UploadedFile $xmlFile): ImportedProductsReport;
}