<?php
namespace Database\Seeders;

use App\Models\Author;
use App\Services\Helpers;
use Illuminate\Database\Seeder;

class BooksAuthorSeeder extends Seeder
{
    public function run()
    {
        $authors = Helpers::csvToArray(__DIR__ . '/assets/authors.csv');

        foreach($authors as $key => $author)
        {
            unset($authors[$key]);

            if($key != 0) {
                $authors[$key]['id'] = $key;
                $authors[$key]['author_id'] = $author[0] ? : null;
                $authors[$key]['author_name'] = $author[1] ? : null;
            }
        }

        foreach(array_chunk($authors, 1000) as $cats) {
            Author::upsert($cats, ['author_id']);
        }

        unset($authors);
    }
}
