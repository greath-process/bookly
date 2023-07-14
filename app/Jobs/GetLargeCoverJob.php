<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\ImageGenerate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetLargeCoverJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private array $book)
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $big_image = $this->book['isbn']
            ? (new ImageGenerate)->getLargeCover($this->book['isbn'])
            : null;

        $book = Book::where('volume_id', $this->book['id'])->first();
        $book->big_image = $big_image;
        $book->save();
    }
}
