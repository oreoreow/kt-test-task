<?php

namespace App\Products\Infrastructure\Service;
ini_set("memory_limit", "500M");

use App\Products\Domain\Factory\ImportedProductsReportFactory;
use App\Products\Domain\Factory\ProductFactory;
use App\Products\Domain\Repository\ProductRepositoryInterface;
use App\Products\Domain\Service\ProductsImportServiceInterface;
use App\Shared\Domain\Service\XmlProcessingServiceInterface;
use App\Shared\Infrastructure\Service\XmlProcessingService;
use Doctrine\ORM\Query\ResultSetMapping;
use DOMDocument;
use DOMXPath;
use Exception;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Products\Domain\Entity\ImportedProductsReport;
use XMLReader;

class ProductsImportService implements ProductsImportServiceInterface
{
    public function __construct(
        private ProductFactory $productFactory,
        private ProductRepositoryInterface $productRepository,
        private ImportedProductsReportFactory $importedProductsReportFactory
    )
    {

    }


    public function importProductsFromXML(UploadedFile $xmlFile): ImportedProductsReport
    {
        $xml = new XMLReader();
        $xml->open('compress.zlib://'.$xmlFile->getPathname());
        $csvFileName = $xmlFile->getPath().'/'.microtime().'.csv';
        $csvFp = fopen($csvFileName, 'w');

        $productsCountBeforeImport = $this->productRepository->count([]);

        while($xml->read() && $xml->name != 'product'){}
        $invalidLines = 0;
        $requestedProductsCount = 0;

        while($xml->name == 'product')
        {
            $requestedProductsCount++;
            $element = null;
            try {
                $element = new SimpleXMLElement($xml->readOuterXML());
                fputcsv($csvFp, [
                    $element->name,
                    $element->description,
                    $element->weight,
                    $element->category
                ]);
            } catch (\Throwable $t) {
                $invalidLines++;
            }
            $xml->next('product');
            unset($element);
        }
        $successImport = $this->productRepository->importProductsFromCSV($csvFileName);

        $savedProductsCount = $successImport ? $this->productRepository->count([]) - $productsCountBeforeImport : 0;

        fclose($csvFp);
        unlink($csvFileName);

        return $this->importedProductsReportFactory->create(
            $requestedProductsCount,
            $savedProductsCount,
            $invalidLines
        );
    }
}