<?php

namespace App\Shared\Infrastructure\Controller;

use App\Products\Domain\Factory\ProductFilterFactory;
use App\Products\Domain\Repository\ProductRepositoryInterface;
use App\Products\Domain\Service\ProductsFilterServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products")
     * @param ProductFilterFactory $productFilterFactory
     * @param Request                    $request
     *
     * @return Response
     */
    public function index(
        ProductFilterFactory $productFilterFactory,
        ProductsFilterServiceInterface $productsFilterService,
        Request $request
    ): Response {

        $productFilter = $productFilterFactory->create(
            $request->get('orderBy', 'name'),
            $request->get('order', 'asc'),
            $request->get('limit', 50),
            $request->get('page', 1),
            $request->get('category'),
            $request->get('minWeight', 0),
            $request->get('maxWeight', 0),
        );

        $filteredProducts = $productsFilterService->getFilteredProducts($productFilter);
        $totalProducts = $productsFilterService->getFilteredProductsCount($productFilter);

        $maxPages = 6;
        return $this->render('products.html.twig', [
            'products' => [
                'list' => $filteredProducts,
                'categories' => [
                    'list' => $productsFilterService->getCategories()
                ],
                'total' => $totalProducts,
                'pages' => [
                    'total' => ceil($totalProducts/$productFilter->getLimit()),
                    'showMin' => ceil($totalProducts/$productFilter->getLimit()) > $maxPages && $productFilter->getPage() > $maxPages/2 ? $productFilter->getPage() - ceil($maxPages/2) : 1,
                    'showMax' => $productFilter->getPage() > $maxPages/2 ? min(ceil($totalProducts/$productFilter->getLimit()), $productFilter->getPage() + floor($maxPages/2)) : $maxPages,
                ],
                'filter' => [
                    'orderBy' => $productFilter->getOrderBy(),
                    'order' => $productFilter->getOrder(),
                    'limit' => $productFilter->getLimit(),
                    'page' => $productFilter->getPage(),
                    'category' => $productFilter->getCategory(),
                    'minWeight' => $productFilter->getMinGWeight(),
                    'maxWeight' => $productFilter->getMaxGWeight(),
                ]
            ],
        ]);
    }
}
