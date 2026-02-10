<?php

namespace App\Service\Paginator;

use RuntimeException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

class Paginator implements PaginatorInterface
{
    private int $count = 0;
    private array $items = [];
    private int $limit;
    private int $page;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(private readonly RequestStack $requestStack)
    {
        $request = $this->requestStack->getCurrentRequest();
        $pageFromRequest = $request->get('page', 1);
        $this->page = max($pageFromRequest, 1);

        $limitFromRequest = $request->get('limit',  24);
        $this->limit = min(max($limitFromRequest, 1), 48);
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getPage(): int
    {
        return $this->page;
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
        return $this->limit;
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
