<?php

namespace App\Console\Commands;

use App\Jobs\FetchGuardianArticles;
use App\Jobs\FetchNewYorkTimesArticles;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PollNewYorkTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:poll-new-york-times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle(): void
    {
        $lastBatch = DB::table('job_batches')
            ->where('name', 'poll-nyt')
            ->whereNotNull('finished_at')
            ->orderByDesc('finished_at')
            ->first();

        $latestDate = Carbon::parse($lastBatch?->finished_at);

        if ($lastBatch && abs(now()->diffInMinutes($latestDate)) < 5) {
            return;
        }

        $params = [
            'api-key' => config('services.new-york-times.key'),
            'begin_date' => now()->subDay()->format('Ymd'),
            'end_date' => now()->format('Ymd'),
            'sort' => 'newest',
            'page' => 0,
            'fq' => 'type_of_material:("News")',
        ];

        $response = Http::get(config('services.new-york-times.url') . 'articlesearch.json', $params);

        if (!$response->successful()) {
            return;
        }

        $perPage = 10;
        $totalResults = $response->json()['response']['meta']['hits'];
        $pages = ceil($totalResults / $perPage);

        $jobs = collect(range(1, $pages))->map(function ($page, $i) use ($params) {
            return (new FetchNewYorkTimesArticles([
                ...$params,
                'page' => $page
            ]))->delay(now()->addSeconds($i + 1));
        });

        Bus::batch($jobs)->name('poll-nyt')->dispatch();
    }
}
