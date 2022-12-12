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

    public function testImportRequestRespondedSuccessfulResult(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/import');

        $this->assertResponseIsSuccessful();
    }

    public function testImportXMLRequestRespondedSuccessfulResult(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/import/products.xml');

        $this->assertResponseIsSuccessful();
    }
}