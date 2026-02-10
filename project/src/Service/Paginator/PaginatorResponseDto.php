<?php

namespace App\Service\Paginator;


class PaginatorResponseDto
{
    public static function response(PaginatorInterface $paginator): array
    {
        return [
            'data' => $paginator->getItems(),
            'meta' => [
                'page' => $paginator->getPage(),
                'limit' => $paginator->getLimit(),
                'count' => $paginator->getCount(),
                'pagesCount' => $paginator->getPagesCount(),
            ],
        ];
    }
}
