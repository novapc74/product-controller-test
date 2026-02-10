<?php

namespace App\Service\Paginator;

use RuntimeException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class Paginator implements PaginatorInterface
{
    private const int LIMIT_PER_PAGE = 24;

    private int $count = 0;
    private array $items = [];

    private PaginatorRequestDto $requestDto;

    /**
     * @param Request $request
     * @param int|null $limit
     */
    public function __construct(private readonly Request $request, ?int $limit = self::LIMIT_PER_PAGE)
    {
        $this->requestDto = PaginatorRequestDto::fromRequest($this->request, $limit);
    }

    public function getPage(): int
    {
        return $this->requestDto->getPage();
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLimit(): int
    {
        return $this->requestDto->getLimit();
    }

    public function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getLimit();
    }

    public function getPagesCount(): int
    {
        return ceil($this->getCount() / $this->getLimit());
    }

    public function paginate(array $collection, ?int $count = null): self
    {
        if ($count === null) {
            $offset = $this->getOffset();
            $limit = $this->getLimit();

            $this->items = array_slice($collection, $offset, $limit);
            $this->count = count($collection);

            return $this;
        }

        $this->items = $collection;
        $this->count = $count;

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function paginateQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->setMaxResults($this->getLimit())
            ->setFirstResult($this->getOffset());
    }

    public function paginateSql(string &$sql): void
    {
        $sql .= " LIMIT {$this->getLimit()} OFFSET {$this->getOffset()}";
    }
}
