<?php

namespace App\Console\Commands;

use App\Jobs\FetchGuardianArticles;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Throwable;

class PollTheGuardian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:poll-the-guardian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws Throwable
     */
    public function handle(): void
    {
        $latestDate = Article::latest()
            ->where('source', 'the-guardian')
            ->first()?->created_at;

        # Only fetch after 24 hours have passed since last fetch
        if ($latestDate && abs(now()->diffInHours()) < 24) {
            return;
        }

        $params = [
            'api-key' => config('services.the-guardian.key'),
            'from-date' => ($latestDate ?? now()->subDay())->format('Y-m-d'),
            'to-date' => now()->format('Y-m-d'),
            'order-by' => 'newest',
            'page-size' => 50,
            'page' => 1,
        ];

        $response = Http::get(config('services.the-guardian.url') . 'search', $params);

        if (!$response->successful()) {
            return;
        }

        $pages = $response->json()['response']['pages'];

        $jobs = collect(range(1, $pages))->map(function ($page, $i) use ($params) {
            return (new FetchGuardianArticles([
                ...$params,
                'page' => $page
            ]))->delay(now()->addSeconds($i + 1));
        });

        Bus::batch($jobs)->dispatch();
    }
}
