<?php

namespace App\Jobs;

use App\Services\ImageGenerate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MakeUserImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private $user = null)
    {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new ImageGenerate)->generateImageOpenGraph($this->user);
    }
}
