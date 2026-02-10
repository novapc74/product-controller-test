<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\DBAL\Exception;
use App\Dto\ProductSearchDto;
use Doctrine\DBAL\ParameterType;
use App\Service\Paginator\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws Exception
     */
    public function getProductCountBySearch(ProductSearchDto $dto): int
    {
        $params = $types = [];
        $sql = 'SELECT COUNT(*) FROM product -- search area';

        if ($search = $dto->search) {
            $sql = str_replace('-- search area', ' WHERE LOWER(name) LIKE :search', $sql);
            $params['search'] = "%$search%";
            $types['search'] = ParameterType::STRING;
        }

        return (int) $this->getEntityManager()
            ->getConnection()
            ->fetchOne($sql, $params, $types);
    }

    /**
     * @throws Exception
     */
    public function getProductsBySearch(Paginator $paginator, ProductSearchDto $dto): array
    {
        $params = $types = [];
        $conn = $this->getEntityManager()->getConnection();
        $sortDirection = $dto->sort_direction;

        $sql = "
        SELECT 
            id,
            name,
            price,
            status,
            DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at 
        FROM product p -- search area
        ORDER BY name $sortDirection
    ";

        if ($search = $dto->search) {
            $sql = str_replace('-- search area', ' WHERE LOWER(name) LIKE :search', $sql);
            $params['search'] = "%$search%";
            $types['search'] = ParameterType::STRING;
        }

        $paginator->paginateSql($sql);

        return $conn
            ->executeQuery($sql, $params, $types)
            ->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getProductByIdOrNull(int $id): ?array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT 
            id,
            name,
            price,
            status,
            DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s") as created_at 
        FROM product p 
        WHERE p.id = :id
    ';

        $result = $conn
            ->executeQuery($sql, [
                'id' => $id
            ])
            ->fetchAssociative();

        return $result ?: null;
    }
}
