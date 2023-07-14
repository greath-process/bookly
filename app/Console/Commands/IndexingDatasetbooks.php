<?php

namespace App\Console\Commands;


use App\Models\Datasetbook;
use Illuminate\Console\Command;
use Meilisearch\Client;

class IndexingDatasetbooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datasetbooks_indexing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Datasetbook indexing';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'), new \GuzzleHttp\Client(['timeout' => 0]));
        Datasetbook::makeAllSearchable();
        $client->index('datasetbooks')->updateSearchableAttributes(['title', 'isbn10', 'isbn13', 'authors.name',]);
        $client->index('datasetbooks')->updateSortableAttributes(['bestsellers_rank', 'rating_count', 'rating_avg']);
        $client->index('datasetbooks')->updateFilterableAttributes(['lang']);
        $client->index('datasetbooks')->updateRankingRules([
            'sort',
            'words',
            'typo',
            'proximity',
            'attribute',
            'exactness'
        ]);

        $this->comment('Datasetbook indexing complete');
    }
}
