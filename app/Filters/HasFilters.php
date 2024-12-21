<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;

  trait HasFilters
  {
      public function applyFilters(Builder $query, array $filters): Builder
      {
          foreach ($filters as $filter) {
              $filterInstance = app($filter);

              if ($filterInstance->shouldApply()) {
                  $query = $filterInstance->apply($query);
              }
          }

          return $query;
      }
  }
