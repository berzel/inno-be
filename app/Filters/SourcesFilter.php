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
          $sources = $value ?? explode(',', $this->request->get('sources'));
          $query->whereIn('source', $sources);

          return $query;
      }
  }
