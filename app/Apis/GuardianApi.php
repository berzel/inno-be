<?php

  namespace App\Apis;

  use Carbon\Carbon;
  use Exception;
  use Illuminate\Support\Collection;
  use Illuminate\Support\Facades\Http;
  use Illuminate\Support\Facades\Log;

  class GuardianApi implements NewsApi
  {
      private string $apiKey;
      private string $baseUrl;

      public function __construct()
      {
          $this->apiKey = config('services.the-guardian.key');
          $this->baseUrl = config('services.the-guardian.url');
      }

      private function getDefaultParams(): array
      {
          return [
              'api-key' => $this->apiKey,
              'from-date' => now()->subDay()->format('Y-m-d'),
              'to-date' => now()->format('Y-m-d'),
              'order-by' => 'newest',
              'page-size' => 50,
              'page' => 1,
          ];
      }

      /**
       * @throws Exception
       */
      public function getTotalPages(): int
      {
          $searchEndpoint = $this->baseUrl . 'search';
          $response = Http::get($searchEndpoint, $this->getDefaultParams());

          if (!$response->successful()) {
              Log::error('Failed to get total pages', $response->json());
              throw new Exception('Failed to get total pages');
          }

          return $response->json()['response']['pages'];
      }

      /**
       * @throws Exception
       */
      public function fetchArticles(int $page): Collection
      {
          $searchEndpoint = $this->baseUrl . 'search';
          $params = [...$this->getDefaultParams(), 'page' => $page];

          $response = Http::get($searchEndpoint, $params);

          if (!$response->successful()) {
              Log::error('Failed to fetch articles', $response->json());
              throw new Exception('Failed to fetch articles');
          }

          $results = $response->json()['response']['results'];

          return collect($results)->map(function ($result) {
              return [
                  'title' => $result['webTitle'],
                  'slug' => $result['id'],
                  'source' => 'the-guardian',
                  'category' => $result['sectionId'],
                  'created_at' => Carbon::parse($result['webPublicationDate']),
                  'updated_at' => Carbon::parse($result['webPublicationDate']),
              ];
          });
      }
  }
