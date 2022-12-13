<?php

namespace Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductsCheckActionTest extends WebTestCase
{
    public function testProductsRequestRespondedSuccessfulResult(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/products');

        $this->assertResponseIsSuccessful();
    }

    public function testExportRequestRespondedSuccessfulResult(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/export');

        $this->assertResponseIsSuccessful();
    }

    public function testExportXMLRequestRespondedSuccessfulResult(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/export/products.xml');

        $this->assertResponseIsSuccessful();
    }
}