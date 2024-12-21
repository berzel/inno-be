<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
     */
    public function handle(): void
    {
        $response = Http::get(config('services.new-york-times.url') . 'articlesearch.json', [
            'api-key' => config('services.new-york-times.key'),
            'fq' => 'type_of_material:("News")'
        ]);

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
        })->toArray();

        DB::table('articles')->insert($data);
    }
}
