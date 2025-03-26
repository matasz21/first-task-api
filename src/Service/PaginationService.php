<?php

namespace App\Service;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\DTO\PaginationDTO;

class PaginationService
{
    public function paginate(Query $query, int $page = 1, int $limit = 10): PaginationDTO
    {
        $page = max(1, $page);
        $limit = max(1, $limit);

        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $totalPages = max(1, (int) ceil($totalItems / $limit));

        if ($totalItems === 0) {
            return new PaginationDTO([], 1, 1, 0);
        }

        if ($page > $totalPages) {
            return new PaginationDTO([], $page, $totalPages, $totalItems);
        }

        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new PaginationDTO(
            items: iterator_to_array($paginator),
            currentPage: $page,
            totalPages: $totalPages,
            totalItems: $totalItems
        );
    }
}
