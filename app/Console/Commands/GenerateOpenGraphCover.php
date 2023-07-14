<?php

namespace App\Console\Commands;


use App\Models\LoginToken;
use App\Services\ImageGenerate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateOpenGraphCover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_opengraph_cover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and generate image for OpenGraph.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filename = $_SERVER['DOCUMENT_ROOT'] . '/cover.png';

        if (!file_exists($filename)) {
            (new ImageGenerate)->generateImageOpenGraph(null);
            $this->comment('Automated Generate Image for OpenGraph Complete');
        } else {
            $this->comment('Automated Check Image for OpenGraph Complete');
        }
    }
}
