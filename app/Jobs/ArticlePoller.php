<?php

namespace App\Jobs;

use App\Apis\NewsApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ArticlePoller implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly NewsApi $api
    ) {
        //
    }

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public int $backoff = 5;

    /**
     * Execute the job.
     * @throws \Throwable
     */
    public function handle(): void
    {
        $lastBatch = DB::table('job_batches')
            ->where('name', get_class($this->api))
            ->whereNotNull('finished_at')
            ->orderByDesc('finished_at')
            ->first();

        $latestDate = Carbon::parse($lastBatch?->finished_at);

        if ($lastBatch && abs(now()->diffInMinutes($latestDate)) < 5) {
            return;
        }

        $pages = $this->api->getTotalPages();

        $jobs = collect(range(1, $pages))->map(function ($page) {
            return (new ArticleFetcher($this->api, $page))->delay(now()->addSeconds($page * 5));
        });

        Bus::batch($jobs)->name(get_class($this->api))->dispatch();
    }
}
