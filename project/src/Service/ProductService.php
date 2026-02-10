<?php

namespace App\Service;

use App\Entity\Product;
use App\Dto\ProductPostDto;
use App\Dto\ProductPatchDto;
use Doctrine\DBAL\Exception;
use App\Dto\ProductSearchDto;
use App\Service\Paginator\Paginator;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Paginator\PaginatorResponseDto;

readonly class ProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository,
        private Paginator              $paginator
    )
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
    public function getAllProducts(ProductSearchDto $dto): array
    {
        $count = $this->productRepository->getProductCountBySearch($dto);

        $collection = $this->productRepository->getProductsBySearch($this->paginator, $dto);

        return PaginatorResponseDto::response(
            $this->paginator->paginate($collection, $count)
        );
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

    public function deleteProduct(Product $product): void
    {
        #TODO так как у товара нет связей, то просто сносим, ничего не проверяем...
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}