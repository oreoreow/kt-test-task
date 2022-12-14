<?php

namespace Functional\Controller;

use App\Products\Domain\Factory\ProductFilterFactory;
use App\Products\Infrastructure\Repository\ProductRepository;
use App\Products\Infrastructure\Service\ProductsFilterService;
use App\Shared\Infrastructure\Controller\ProductController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductsCheckActionTest extends WebTestCase
{
    // private ProductRepository $productRepository;
    //
    // public function setUp(): void
    // {
    //     $this->productRepository = static::getContainer()->get(ProductRepository::class);
    //     parent::setUp();
    // }

    public function testProductsRequestRespondedSuccessfulResult(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/products');

        $this->assertResponseIsSuccessful();
    }

    public function testImportSuccessfulResult(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/import');

        $form = $crawler->selectButton('Start Import')->form();

        $testXmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<products>
    <product>
        <name>NAME</name>
        <description>DESCRIPTION</description>
        <category>CATEGORY</category>
        <weight>1 kg</weight>
    </product>
</products>
XML;
        $testXmlName = __DIR__.'/test.xml';
        $fp = fopen($testXmlName, 'w');
        fwrite($fp, $testXmlString);
        fclose($fp);

        var_dump($form);

        $form['product_import_form[xml_file]']->upload($testXmlName);
        $client->submit($form);

        $pr = static::getContainer()->get(ProductRepository::class);
        $product = $pr->findBy(['name' => 'NAME']);

        $this->assertResponseIsSuccessful();
    }

    public function testInvalidImportSuccessfulResult(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/import');

        $form = $crawler->selectButton('Start Import')->form();

        $testXmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<products>
    <product>
        <name_>NAME</name_>
        <description_>DESCRIPTION</description_>
        <category>CATEGORY</category>
        <weight>1 kg</weight>
    </product>
</products>
XML;
        $testXmlName = __DIR__.'/test.xml';
        $fp = fopen($testXmlName, 'w');
        fwrite($fp, $testXmlString);
        fclose($fp);


        $form['product_import_form[xml_file]']->upload($testXmlName);
        $client->submit($form);

        $pr = static::getContainer()->get(ProductRepository::class);
        $product = $pr->findBy(['name' => 'NAME']);
        var_dump($product);

        $this->assertResponseIsSuccessful();
    }
}