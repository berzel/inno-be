<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  class SourcesFilter extends Filter
  {
      public function shouldApply(): bool
      {
          return !empty($this->request->get('sources'));
      }

      public function apply(Builder $query, $value = null): Builder
      {
          $sources = $value ?? $this->request->get('sources');

          foreach ($sources as $category) {
              $query->orWhere('source', $category);
          }

          return $query;
      }
  }
