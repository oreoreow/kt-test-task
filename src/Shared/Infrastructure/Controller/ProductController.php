<?php

namespace App\Shared\Infrastructure\Controller;

use App\Products\Domain\Entity\ProductFilter;
use App\Products\Domain\Factory\ProductFactory;
use App\Products\Domain\Factory\ProductFilterFactory;
use App\Products\Domain\Form\ProductImportForm;
use App\Products\Domain\Service\ProductsFilterServiceInterface;
use App\Products\Domain\Service\ProductsImportServiceInterface;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

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
     * @Route("/products", name="products")
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
        $totalPages = ceil($totalProducts/$productFilter->getLimit());
        $pagesRef = min($totalPages, 6);
        $test = [
            'list'       => $filteredProducts,
            'categories' => [
                'list' => $this->productsFilterService->getCategories()
            ],
            'total'      => $totalProducts,
            'pages'      => [
                'total'   => $totalPages,
                'showMin' => $totalPages > $pagesRef && $productFilter->getPage() > $pagesRef/2 ? $productFilter->getPage()-ceil($pagesRef/2) : 1,
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
        ];
        // echo "<pre>";
        // var_dump($test['total']);
        // var_dump($test['pages']);
        // echo "</pre>";
        // die();

        return $this->render('products.html.twig', [
            'products' => [
                'list'       => $filteredProducts,
                'categories' => [
                    'list' => $this->productsFilterService->getCategories()
                ],
                'total'      => $totalProducts,
                'pages'      => [
                    'total'   => $totalPages,
                    'showMin' => $totalPages > $pagesRef && $productFilter->getPage() > $pagesRef/2 ? $productFilter->getPage()-ceil($pagesRef/2) : 1,
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
     * @Route("/export")
     * @param Request $request
     *
     * @return Response
     */
    public function export(Request $request): Response
    {
        $productFilter = $this->createProductFilter($request);

        return $this->render('export.html.twig', [
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
     * @Route("/export/products.xml")
     * @param Request $request
     *
     * @return BinaryFileResponse
     */
    public function exportXML(Request $request): BinaryFileResponse
    {
        $productFilter = $this->createProductFilter($request);
        $filteredProducts = $this->productsFilterService->getFilteredProducts($productFilter);

        $xmlContent = $this->renderView('export.xml.html.twig', [
            'products' => $filteredProducts,
        ]);


        $path = $this->getParameter('kernel.project_dir') . '/public/xml/'.microtime().'.xml';
        $fileSystem = new Filesystem();
        $fileSystem->dumpFile($path, $xmlContent);
        $response = new BinaryFileResponse($path);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'products.xml'
        );

        return $response;
    }


    /**
     * @Route("/import", name="import")
     * @param Request                        $request
     * @param ProductImportForm              $productForm
     * @param ProductsImportServiceInterface $productsImportService
     * @param SerializerInterface            $serializer
     *
     * @return Response
     */
    public function import(
        Request                        $request,
        ProductImportForm              $productForm,
        ProductsImportServiceInterface $productsImportService,
        SerializerInterface            $serializer
    ): Response {
        $form = $this->createForm(ProductImportForm::class, $productForm);
        $form->handleRequest($request);

        $renderParamsToMerge = [];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $xmlFile */
            $xmlFile = $form->get('xml_file')->getData();
            $importedProductsReport = $productsImportService->importProductsFromXML($xmlFile);

            $renderParamsToMerge = [
                'importedProductsReport' => [
                    'requestedCount' => $importedProductsReport->getRequestedProductsCount(),
                    'savedCount' => $importedProductsReport->getSavedProductsCount()
                ]
            ];
        }

        return $this->render('import.html.twig', array_merge([
            'form' => $form->createView(),
        ], $renderParamsToMerge));
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
