<?php

namespace App\Console\Commands;


use App\Models\LoginToken;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RecycleExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recycle_expired_tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recycle Tokens That Are Expired.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $current = Carbon::now();
        $tokens = LoginToken::where('expires_at', '<', $current)->get();

        foreach ($tokens as $invite) {
            $invite->delete();
        }

        $this->comment('Automated Recycle Expired Tokens Command Complete');
    }
}
