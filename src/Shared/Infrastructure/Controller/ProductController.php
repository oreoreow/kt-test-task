<?php

namespace App\Shared\Infrastructure\Controller;

use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Factory\ProductFilterFactory;
use App\Products\Domain\Service\ProductsFilterServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private ProductFilterFactory $productFilterFactory;
    private ProductsFilterServiceInterface $productsFilterService;
    public function __construct(
        ProductFilterFactory           $productFilterFactory,
        ProductsFilterServiceInterface $productsFilterService,
    ) {
        $this->productFilterFactory = $productFilterFactory;
        $this->productsFilterService = $productsFilterService;
    }

    /**
     * @Route("/products")
     * @Route("/")
     * @param Request $request
     *
     * @return Response
     */
    public function products( Request $request): Response
    {

        $productFilter = $this->createProductFilter($request);

        if(!$productFilter->getLimit()) $productFilter->setLimit(50);

        $filteredProducts = $this->productsFilterService->getFilteredProducts($productFilter);
        $totalProducts    = $this->productsFilterService->getFilteredProductsCount($productFilter);

        $pagesRef = 6;

        return $this->render('products.html.twig', [
            'products' => [
                'list'       => $filteredProducts,
                'categories' => [
                    'list' => $this->productsFilterService->getCategories()
                ],
                'total'      => $totalProducts,
                'pages'      => [
                    'total'   => ceil($totalProducts/$productFilter->getLimit()),
                    'showMin' => ceil($totalProducts/$productFilter->getLimit()) > $pagesRef && $productFilter->getPage() > $pagesRef/2 ? $productFilter->getPage()-ceil($pagesRef/2) : 1,
                    'showMax' => $productFilter->getPage() > $pagesRef/2 ? min(ceil($totalProducts/$productFilter->getLimit()), $productFilter->getPage()+floor($pagesRef/2)) : $pagesRef,
                ],
                'filter'     => [
                    'orderBy'   => $productFilter->getOrderBy(),
                    'order'     => $productFilter->getOrder(),
                    'limit'     => $productFilter->getLimit(),
                    'page'      => $productFilter->getPage(),
                    'category'  => $productFilter->getCategory(),
                    'minWeight' => $productFilter->getMinGWeight(),
                    'maxWeight' => $productFilter->getMaxGWeight(),
                ]
            ],
        ]);
    }

    /**
     * @Route("/import")
     * @param Request $request
     *
     * @return Response
     */
    public function import(Request $request): Response
    {
        $productFilter = $this->createProductFilter($request);

        return $this->render('import.html.twig', [
            'products' => [
                'categories' => [
                    'list' => $this->productsFilterService->getCategories()
                ],
                'total' => $this->productsFilterService->getFilteredProductsCount($productFilter),
                'filter'     => [
                    'orderBy'   => $productFilter->getOrderBy(),
                    'order'     => $productFilter->getOrder(),
                    'limit'     => $productFilter->getLimit(),
                    'page'      => $productFilter->getPage(),
                    'category'  => $productFilter->getCategory(),
                    'minWeight' => $productFilter->getMinGWeight(),
                    'maxWeight' => $productFilter->getMaxGWeight(),
                ]
            ],
        ]);
    }

    /**
     * @Route("/import/products.xml")
     * @param Request $request
     *
     * @return Response
     */
    public function importXML(Request $request): Response
    {
        $productFilter = $this->createProductFilter($request);
        $filteredProducts = $this->productsFilterService->getFilteredProducts($productFilter);

        $response = new Response(
            $this->renderView('import.xml.html.twig', [
                'products' => $filteredProducts,
            ])
        );
        $response->headers->set('Content-type', 'text/xml');

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return ProductFilter
     */
    private function createProductFilter(Request $request): ProductFilter
    {
        return  $this->productFilterFactory->create(
            $request->get('orderBy', 'name'),
            $request->get('order', 'asc'),
            $request->get('limit'),
            $request->get('page', 1),
            $request->get('category'),
            $request->get('minWeight', 0),
            $request->get('maxWeight', 0),
        );
    }
}
