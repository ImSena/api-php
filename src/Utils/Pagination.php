<?php

namespace App\Utils;

class Pagination
{
    public static function calculateTotalPages(int $totalRecords, int $limitPerPage): int
    {
        $totalPages = ceil($totalRecords / $limitPerPage);

        if ($totalPages < 1) {
            $totalPages = 1;
        }

        return $totalPages;
    }
}