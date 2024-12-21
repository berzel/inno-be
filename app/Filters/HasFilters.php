<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  trait HasFilters
  {
      public function applyFilters(Builder $query, array $filters, Request $request = null): Builder
      {
          if (is_null($request)) {
              $request = request();
          }

          foreach ($filters as $filter) {
              $filterInstance = new $filter($request);

              if ($filterInstance->shouldApply()) {
                  $query = $filterInstance->apply($query);
              }
          }

          return $query;
      }
  }
