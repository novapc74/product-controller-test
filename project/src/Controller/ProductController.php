<?php

namespace App\Controller;

use App\Entity\Product;
use App\Dto\ProductPostDto;
use Doctrine\DBAL\Exception;
use App\Dto\ProductPatchDto;
use App\Dto\ProductSearchDto;
use App\Service\ProductService;
use App\Service\Paginator\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProductController extends AbstractController
{
    public function __construct(private readonly ProductService $service)
    {
    }

    /**
     * @throws Exception
     */
    #[Route('/product', name: 'app_product')]
    public function index(#[MapQueryString] ?ProductSearchDto $dto = null): JsonResponse
    {
        return $this->json($this->service->getAllProducts($dto ?? new ProductSearchDto()));
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    #[Route('/product/{id}', name: 'app_product_by_id', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showProduct(int $id): JsonResponse
    {
        if (!$product = $this->service->getProductById($id)) {
            throw $this->createNotFoundException(sprintf('Product for id: %s not found', $id));
        }

        return $this->json(['success' => true, 'product' => $product]);
    }

    /**
     * @throws Exception
     */
    #[Route('/product/create', name: 'app_create_product', methods: ['POST'])]
    public function createProduct(#[MapRequestPayload] ProductPostDto $productPostDto): JsonResponse
    {
        $productData = $this->service->createNewProduct($productPostDto);

        return $this->json([
            'success' => true,
            'product' => $productData,
        ], Response::HTTP_CREATED);
    }

    /**
     * @throws Exception
     */
    #[Route('/product/{id}', name: 'app_update_product', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateProduct(
        #[MapRequestPayload] ProductPatchDto $productPatchDto,
        Product                              $product
    ): JsonResponse
    {
        $productData = $this->service->updateProduct($product, $productPatchDto);

        return $this->json([
            'success' => true,
            'product' => $productData,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function destroyProduct(Product $product): JsonResponse
    {
        $this->service->deleteProduct($product);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
