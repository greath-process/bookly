<?php
namespace Database\Seeders;

use App\Models\Author;
use App\Models\Datasetbook;
use App\Models\Format;
use App\Services\Helpers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksDataUpdateSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $dataset = Helpers::csvToArray(__DIR__ . '/assets/dataset-w-urls.csv');
        if($dataset) {
            $formats = array_flip(Format::all()->pluck('format_id', 'id')->toArray());
            $authors = array_flip(Author::all()->pluck('author_id', 'id')->toArray());
            $dataset_authors = [];
            $dataset_authors_id = 1;

            foreach($dataset as $key => $book)
            {
                unset($dataset[$key]);

                if($key != 0) {
                    $dataset[$key] = [
                        'id' => $key,
                        'bestsellers_rank' => isset($book[1]) && $book[1] ? $book[1] : null,
                        'format_id' => isset($book[5]) && isset($formats[ $book[5] ]) ? $formats[ $book[5] ] : null,
                        'book_id' => isset($book[6]) && $book[6] ? $book[6] : null,
                        'image_url' => isset($book[7]) && $book[7] ? $book[7] : null,
                        'isbn10' => isset($book[9]) && $book[9] ? $book[9] : null,
                        'isbn13' => isset($book[10]) && $book[10] ? $book[10] : null,
                        'lang' => isset($book[11]) && $book[11] ? $book[11] : null,
                        'publication_date' => isset($book[12]) && !empty($book[12]) ? $book[12] : null,
                        'rating_avg' => isset($book[14]) && $book[14] ? $book[14] : null,
                        'rating_count' => isset($book[15]) && $book[15] ? $book[15] : null,
                        'title' => isset($book[16]) && $book[16] ? $book[16] : 'Untitled',
                    ];
                }

                if (isset($book[0]) && !is_array($book[0])) {
                    $book[0] = str_replace(["]","["], "", $book[0]);
                    $book[0] = explode(', ', $book[0]);
                }

                if (is_array($book[0]) && !empty($book[0])) {
                    foreach($book[0] as $author_id){
                        if(isset($authors[ $author_id ])) {
                            $dataset_authors[] = [
                                'id' => $dataset_authors_id,
                                'author_id' => $authors[ $author_id ],
                                'datasetbook_id' => $key,
                            ];
                            $dataset_authors_id++;
                        }
                    }
                }
            }

            foreach(array_chunk($dataset, 3000) as $cats) {
                Datasetbook::upsert($cats, 'book_id');
            }
            unset($formats, $cats);

            foreach(array_chunk($dataset_authors, 2000) as $cats) {
                DB::table('author_datasetbook')->upsert($cats, 'id');
            }
            unset($dataset_authors, $cats);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
