<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
     */
    public function handle(): void
    {

        $response = Http::get(config('services.the-guardian.url') . 'search', [
            'api-key' => config('services.the-guardian.key'),
        ]);

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
