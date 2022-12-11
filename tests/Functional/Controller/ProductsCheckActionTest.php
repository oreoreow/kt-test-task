<?php

namespace Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductsCheckActionTest extends WebTestCase
{
    public function testRequestRespondedSuccessfulResult(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/products');

        $this->assertResponseIsSuccessful();
    }
}