<?php

namespace App\Console\Commands;

use App\Apis\GuardianApi;
use App\Apis\NewYorkTimesApi;
use App\Jobs\ArticlePoller;
use Illuminate\Console\Command;

class ArticlePollerTrigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:poll-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $apis = [
            GuardianApi::class,
            NewYorkTimesApi::class,
        ];

        foreach ($apis as $api) {
            dispatch(new ArticlePoller(app($api)));
        }
    }
}
