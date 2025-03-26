<?php

namespace App\DTO;

class PaginationDTO
{
    public function __construct(
        public readonly array $items,
        public readonly int $currentPage,
        public readonly int $totalPages,
        public readonly int $totalItems,
    ) {}
}
