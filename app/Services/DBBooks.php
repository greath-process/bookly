<?php

namespace App\Services;

use App\Contracts\BooksSearchInterface;
use App\Models\Datasetbook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Meilisearch\Client;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;


class DBBooks implements BooksSearchInterface
{
    protected const numBooksToGet = 7;
    protected const bookFormatId = [1,2,9];

    public static function getBooks($query): array
    {
        $books = config('books.search_db_logic')
            ? (config('scout.meilisearch.key')
                ? Datasetbook::search(query: $query)
                    ->orderBy('rating_count', 'desc')
                    ->orderBy('rating_avg', 'desc')
                    ->take(self::numBooksToGet)
                    ->get()
                : Search::add(
                Datasetbook::where('bestsellers_rank', '>', 0)->with('authors'),
                ['title', 'authors.author_name', 'isbn10', 'isbn13'],
                'bestsellers_rank')
            ->simplePaginate(self::numBooksToGet, 'page', 1)
            ->search($query))
            : Datasetbook::query()
            ->where('title', 'LIKE', '%'.$query.'%')
            ->orWhereRelation('authors', 'author_name', 'LIKE', '%'.$query.'%')
            ->orWhere('isbn10', $query)
            ->orWhere('isbn13', $query)
            ->orderByDesc('rating_count')
            ->orderByDesc('rating_avg')
            ->limit(self::numBooksToGet)
            ->get();

        return self::setBooksFormat($books);
    }

    public static function setBooksFormat(Paginator|Collection|array $responseArray, array $books = []): array
    {
        foreach($responseArray as $book) {
            $books[] = [
                'title' => $book->title,
                'published' => $book->publication_date,
                'image' => file_exists(public_path() .'/'. $book->image_path) ? '/'.$book->image_path : $book->image_url,
                'id' => $book->book_id,
                'author' => $book->authors()?->first()?->author_name
                    ? implode(', ', $book->authors()?->pluck('author_name')?->toArray())
                    : __('profile.no_author'),
                'isbn' => $book->isbn10,
                'isbn13' => $book->isbn13,
            ];
        }

        return $books;
    }
}
