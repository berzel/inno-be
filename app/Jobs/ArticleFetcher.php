<?php

namespace App\Jobs;

use App\Apis\NewsApi;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleFetcher implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public int $backoff = 10;

    public int $tries = PHP_INT_MAX;

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
                ->map(function ($item) {
                    $category = Category::firstOrCreate([
                        'slug' => $item['category']['slug'],
                    ], $item['category']);

                    $item['category_id'] = $category->id;
                    unset($item['category']);

                    return $item;
                })
                ->toArray();

            DB::table('articles')->insert($articles);
        } catch (\Throwable $th) {
            $this->attempts() <= 30
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
