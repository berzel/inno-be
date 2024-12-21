<?php

namespace App\Jobs;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchNewYorkTimesArticles implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly array $params
    ) {
        //
    }

    public function backoff(): int
    {
        return 3;
    }

    public function retryUntil(): \Illuminate\Support\Carbon
    {
        return now()->addHours(24);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $searchEndpoint = config('services.new-york-times.url') . 'articlesearch.json';
        $response = Http::get($searchEndpoint, $this->params);

        if (!$response->successful()) {
            return;
        }

        $results = $response->json()['response']['docs'];

        if (!count($results)) {
            return;
        }

        $data = collect($results)->map(function ($result) {
            return [
                'title' => $result['headline']['main'],
                'slug' => $result['_id'],
                'source' => 'new-york-times',
                'category' => $result['section_name'],
                'created_at' => Carbon::parse($result['pub_date']),
                'updated_at' => Carbon::parse($result['pub_date']),
            ];
        })
            ->filter(fn($item) => !Article::whereSlug($item['slug'])->exists())
            ->toArray();

        DB::table('articles')->insert($data);
    }
}
