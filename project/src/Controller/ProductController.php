<?php

namespace App\Controller;

use App\Entity\Product;
use App\Dto\ProductPostDto;
use Doctrine\DBAL\Exception;
use App\Dto\ProductPatchDto;
use App\Dto\ProductSearchDto;
use App\Service\ProductService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    public function __construct(private readonly ProductService $service)
    {
    }

    #[Route('/product', name: 'app_product')]
    public function index(#[MapQueryString] ?ProductSearchDto $dto = null): JsonResponse
    {
        try {

            return $this->json(
                $this->service->getAllProducts($dto ?? new ProductSearchDto())
            );

        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/product/{id}', name: 'app_product_by_id', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showProduct(int $id): JsonResponse
    {
        try {
            if ($productData = $this->service->getProductById($id)) {
                return $this->json([
                    'product' => $productData,
                ]);
            }
        } catch (Exception $e) {
            #TODO ошибку и метод в логер ...
            # на время тестирования ... потом убрать ошибку сервера...
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/product/create', name: 'app_create_product', methods: ['POST'])]
    public function createProduct(
        #[MapRequestPayload] ProductPostDto $productPostDto
    ): JsonResponse
    {
        try {
            if ($productData = $this->service->createNewProduct($productPostDto)) {
                return $this->json([
                    'product' => $productData,
                ]);
            }
        } catch (Exception $exception) {
            #TODO ошибку и метод в логер ...
            return $this->json([
                'error' => $exception->getMessage(),
            ]);
        }

        return $this->json(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Route('/product/{id}', name: 'app_update_product', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateProduct(
        #[MapRequestPayload] ProductPatchDto $productPatchDto,
        Product                              $product
    ): JsonResponse
    {
        try {
            if ($productData = $this->service->updateProduct($product, $productPatchDto)) {
                return $this->json([
                    'product' => $productData,
                ]);
            }
        } catch (Exception $exception) {
            #TODO ошибку и метод в логер ... потом убрать вывод ошибки клиенту...
            return $this->json([
                'error' => $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }

        return $this->json(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Route('/product/{id}', name: 'app_delete_product', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function destroyProduct(Product $product): JsonResponse
    {
        $this->service->deleteProduct($product);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
