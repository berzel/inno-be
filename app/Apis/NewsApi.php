<?php

  namespace App\Apis;

  use Illuminate\Support\Collection;

  interface NewsApi
  {

      public function getTotalPages(): int;

      public function fetchArticles(int $page): Collection;
  }
