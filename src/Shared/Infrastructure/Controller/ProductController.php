<?php

namespace App\Shared\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products")
     * @return Response
     */
    public function index(): Response
    {
        // TODO: add pagination and filters
        return $this->render('products.html.twig', [
            'controller_name' => self::class,
        ]);
    }
}
