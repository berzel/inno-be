<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchGuardianArticles implements ShouldQueue
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
        $searchEndpoint = config('services.the-guardian.url') . 'search';
        $response = Http::get($searchEndpoint, $this->params);

        if (!$response->successful()) {
            return;
        }

        $results = $response->json()['response']['results'];

        if (!count($results)) {
            return;
        }

        $data = collect($results)->map(function ($result) {
            return [
                'title' => $result['webTitle'],
                'slug' => $result['id'],
                'source' => 'the-guardian',
                'category' => $result['sectionId'],
                'created_at' => Carbon::parse($result['webPublicationDate']),
                'updated_at' => Carbon::parse($result['webPublicationDate']),
            ];
        })->toArray();

        DB::table('articles')->insert($data);
    }
}
