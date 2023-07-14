<?php

declare(strict_types=1);

namespace App\Contracts;

interface BooksSearchInterface
{
    public static function getBooks(string $query): array;

    public static function setBooksFormat(array $responseArray, array $books = []): array;
}
