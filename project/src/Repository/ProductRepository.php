<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
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
    public function getAllProducts(?int $limit = 24, ?int $offset = 0): array
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
        LIMIT :limit
        OFFSET :offset
    ';

        return $conn
            ->executeQuery(
                $sql,
                [
                    'limit' => $limit,
                    'offset' => $offset
                ],
                [
                    'limit' => ParameterType::INTEGER,
                    'offset' => ParameterType::INTEGER
                ]
            )
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
