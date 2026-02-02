<?php

namespace App\Controller;

use App\Dto\ProductPatchDto;
use App\Dto\ProductPostDto;
use App\Entity\Product;
use Doctrine\DBAL\Exception;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    public function __construct(private readonly ProductService $service)
    {
    }

    #[Route('/product', name: 'app_product')]
    public function index(): JsonResponse
    {
        try {
            $products = $this->service->getAllProducts();

            return new JsonResponse(
                json_encode($products, JSON_UNESCAPED_UNICODE), Response::HTTP_OK, [], true
            );
        } catch (Exception $exception) {
            #TODO ошибку и метод в логер ...
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/product/{id}', name: 'app_product_by_id', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showProduct(int $id): JsonResponse
    {
        try {
            if ($productData = $this->service->getProductById($id)) {
                return new JsonResponse(
                    json_encode($productData, JSON_UNESCAPED_UNICODE), Response::HTTP_OK, [], true
                );
            }
        } catch (Exception $exception) {
            #TODO ошибку и метод в логер ...
            return new JsonResponse('server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/product/create', name: 'app_create_product', methods: ['POST'])]
    public function createProduct(
        #[MapRequestPayload] ProductPostDto $productPostDto
    ): JsonResponse
    {
        try {
            if ($productData = $this->service->createNewProduct($productPostDto)) {
                return new JsonResponse(
                    json_encode($productData, JSON_UNESCAPED_UNICODE), Response::HTTP_OK, [], true
                );
            }
        } catch (Exception $exception) {
            #TODO ошибку и метод в логер ...
            return new JsonResponse('server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Route('/product/{id}', name: 'app_update_product', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateProduct(
        #[MapRequestPayload] ProductPatchDto $productPatchDto,
        Product $product
    ): JsonResponse
    {
        try {
            if ($productData = $this->service->updateProduct($product, $productPatchDto)) {
                return new JsonResponse(
                    json_encode($productData, JSON_UNESCAPED_UNICODE), Response::HTTP_OK, [], true
                );
            }
        } catch (Exception $exception) {
            #TODO ошибку и метод в логер ...
            return new JsonResponse('server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Route('/product/{id}', name: 'app_update_product', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function destroyProduct(Product $product): JsonResponse
    {
       $this->service->deleteProduct($product);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
