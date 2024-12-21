<?php

namespace App\Console\Commands;

use App\Jobs\FetchGuardianArticles;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
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
        $lastBatch = DB::table('job_batches')
            ->where('name', 'poll-guardian')
            ->whereNotNull('finished_at')
            ->orderByDesc('finished_at')
            ->first();

        $latestDate = Carbon::parse($lastBatch?->finished_at);

        if ($lastBatch && abs(now()->diffInMinutes($latestDate)) < 5) {
            return;
        }

        $params = [
            'api-key' => config('services.the-guardian.key'),
            'from-date' =>now()->subDay()->format('Y-m-d'),
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

        Bus::batch($jobs)->name('poll-guardian')->dispatch();
    }
}
