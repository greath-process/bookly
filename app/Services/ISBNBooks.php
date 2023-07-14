<?php

namespace App\Services;

use App\Contracts\BooksSearchInterface;

class ISBNBooks implements BooksSearchInterface
{
    protected const urlApi = 'https://api2.isbndb.com/';
    protected const urlSearch = 'books/';
    protected const urlIbsn = 'book/';
    protected const numBooksToGet = 10;
    protected const page = 1;
    protected const column = '';
    protected const with_prices = 0;

    public static function getBooks($query): array
    {
        $headers = array(
            "Content-Type: application/json",
            "Authorization: " . config('books.isbn_key')
        );

        if(is_numeric($query) && strlen($query) >= 10) {
            $url = self::urlApi . self::urlIbsn . $query . '?with_prices=' . self::with_prices;
        } else {
            $request = [
                'pageSize' => self::numBooksToGet,
                'page' => self::page,
                'column' => self::column,
            ];

            $url = self::urlApi . self::urlSearch . urlencode($query) .'?'. http_build_query($request);
        }

        $rest = curl_init();
        curl_setopt($rest,CURLOPT_URL,$url);
        curl_setopt($rest,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($rest,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($rest,CURLOPT_SSL_VERIFYPEER,FALSE);
        $response = curl_exec($rest);
        curl_close($rest);

        return self::setBooksFormat(json_decode($response, true));
    }

    public static function setBooksFormat(array $responseArray, array $books = []): array
    {
        if (isset($responseArray['errorMessage']) || isset($responseArray['message'])) {
            return [];
        }

        $responseArray['books'] = $responseArray['books'] ?? [$responseArray['book']];
        foreach($responseArray['books'] as $book) {
            $books[] = [
                'title' => $book['title'],
                'published' => isset($book['date_published']) ? $book['date_published'] : '',
                'image' => $book['image'],
                'id' => $book['isbn13'],
                'author' => isset($book['authors']) ? current($book['authors']) : '',
                'isbn' => isset($book['isbn']) ? $book['isbn'] : '',
                'isbn13' => isset($book['isbn13']) ? $book['isbn13'] : '',
            ];
        }

        return $books;
    }
}
