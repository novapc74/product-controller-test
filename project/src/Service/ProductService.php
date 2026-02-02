<?php

namespace App\Service;

use App\Dto\ProductPatchDto;
use App\Dto\ProductPostDto;
use App\Entity\Product;
use Doctrine\DBAL\Exception;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository)
    {
    }

    /**
     * @throws Exception
     */
    public function getProductById(int $id): ?array
    {
        return $this->productRepository->getProductByIdOrNull($id);
    }

    /**
     * @throws Exception
     */
    public function getAllProducts(): array
    {
        #TODO тут пагинацию нужно добавить в простом виде через LIMIT/OFFSET и колличество страниц
        # решается в два запроса
        # 1. первый получаем количество для вычисдления OFFSET (...?page=)
        # 2. сам запрос...
        return $this->productRepository->getAllProducts();
    }

    /**
     * @throws Exception
     */
    public function createNewProduct(ProductPostDto $productPostDto): array
    {
        $product = (new Product())
            ->setName($productPostDto->name)
            ->setPrice($productPostDto->price)
            ->setStatus($productPostDto->status);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->getProductById($product->getId());
    }

    /**
     * @throws Exception
     */
    public function updateProduct(Product $product, ProductPatchDto $productPatchDto): array
    {
        $product->setName($productPatchDto->name ?? $product->getName())
            ->setPrice($productPatchDto->price ?? $product->getPrice())
            ->setStatus($productPatchDto->status ?? $product->isStatus()
            );

        $this->entityManager->flush();
        return $this->getProductById($product->getId());
    }
}