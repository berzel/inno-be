<?php

  namespace App\Apis;

  use Carbon\Carbon;
  use Exception;
  use Illuminate\Support\Collection;
  use Illuminate\Support\Facades\Http;
  use Illuminate\Support\Facades\Log;
  use Illuminate\Support\Str;

  class NewYorkTimesApi implements NewsApi
  {

      private function getDefaultParams(): array
      {
          return [
              'api-key' => config('services.new-york-times.key'),
              'begin_date' => now()->subDay()->format('Ymd'),
              'end_date' => now()->format('Ymd'),
              'sort' => 'newest',
              'page' => 0,
              'fq' => 'type_of_material:("News")',
          ];
      }

      /**
       * @throws Exception
       */
      public function getTotalPages(): int
      {
          $params = $this->getDefaultParams();
          $searchEndpoint = config('services.new-york-times.url') . 'articlesearch.json';

          $response = Http::get($searchEndpoint, $params);

          if (!$response->successful()) {
              Log::error('Failed to get total pages', $response->json());
              throw new Exception('Failed to get total pages');
          }

          $perPage = 10; // the default new york time per page value
          $totalResults = $response->json()['response']['meta']['hits'];

          return ceil($totalResults / $perPage);
      }

      /**
       * @throws Exception
       */
      public function fetchArticles(int $page): Collection
      {
          $params = [...$this->getDefaultParams(), 'page' => $page - 1];
          $searchEndpoint = config('services.new-york-times.url') . 'articlesearch.json';

          $response = Http::get($searchEndpoint, $params);

          if (!$response->successful()) {
              Log::error('Failed to fetch articles', $response->json());
              throw new Exception('Failed to fetch articles');
          }

          $results = $response->json()['response']['docs'];

          return collect($results)->map(function ($result) {
              return [
                  'title' => $result['headline']['main'],
                  'slug' => $result['_id'],
                  'source' => 'new-york-times',
                  'category' => [
                      'name' => $result['section_name'],
                      'slug' => Str::slug($result['section_name']),
                  ],
                  'created_at' => Carbon::parse($result['pub_date']),
                  'updated_at' => Carbon::parse($result['pub_date']),
              ];
          });
      }
  }
