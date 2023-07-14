<?php

namespace App\Services;

use App\Contracts\BooksSearchInterface;

class GoogleBooks implements BooksSearchInterface
{
    protected const urlApi = 'https://www.googleapis.com/books/v1/volumes?q=';
    protected const numBooksToGet = 10;
    protected const orderBy = 'relevance';
    protected const projection = 'full';

    public static function getBooks($query, $type = 'intitle:'): array
    {
        $request = [
            'maxResults' => self::numBooksToGet,
            'orderBy' => self::orderBy,
            'projection' => self::projection,
        ];

        if (config('books.key')) {
            $request['key'] = config('books.key');
        }

        $url = self::urlApi . $type . urlencode($query) .'&'. http_build_query($request);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        $bookBatch = json_decode($response, true);

        if ($bookBatch['totalItems'] < 1 && $type == 'intitle:') {
            if(is_numeric($query)) {
                return self::getBooks($query,   'isbn:');
            }

            return self::getBooks($query,  'inauthor:');
        }

        return self::setBooksFormat($bookBatch);
    }

    public static function setBooksFormat(array $responseArray, array $books = []): array
    {
        if (!isset($responseArray['items'])) {
            return [];
        }

        foreach($responseArray['items'] as $book) {
            $books[] = [
                'title' => isset($book['volumeInfo']['title']) ? $book['volumeInfo']['title'] : __('profile.empty'),
                'published' => isset($book['volumeInfo']['publishedDate']) ? $book['volumeInfo']['publishedDate'] : '',
                'image' => isset($book['volumeInfo']['imageLinks']) ? $book['volumeInfo']['imageLinks']['thumbnail'] : '',
                'id' => $book['id'],
                'author' => isset($book['volumeInfo']['authors'])
                    ? current($book['volumeInfo']['authors'])
                    : __('profile.no_author'),
                'isbn' => isset($book['volumeInfo']['industryIdentifiers'])
                    ? current($book['volumeInfo']['industryIdentifiers'])['identifier']
                    : null,
                'isbn13' => isset($book['volumeInfo']['industryIdentifiers'][1])
                    ? $book['volumeInfo']['industryIdentifiers'][1]['identifier']
                    : null,
            ];
        }

        return $books;
    }
}
