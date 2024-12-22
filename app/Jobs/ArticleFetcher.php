<?php

namespace App\Jobs;

use App\Apis\NewsApi;
use App\Models\Article;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleFetcher implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public int $backoff = 10;

    public int $tries = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly NewsApi $api,
        private readonly int $page,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $articles = $this->api->fetchArticles($this->page)
                ->filter(fn($item) => !Article::whereSlug($item['slug'])->exists())
                ->toArray();

            DB::table('articles')->insert($articles);
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());

            $this->attempts() <= $this->tries
                ? $this->release($this->backoff)
                : $this->fail($th);
        }
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping(get_class($this->api)))->releaseAfter(10)];
    }
}
