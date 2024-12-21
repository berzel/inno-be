<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  class SourceFilter extends Filter
  {
      public function shouldApply(): bool
      {
          return !empty($this->request->get('source'));
      }

      public function apply(Builder $query, $value = null): Builder
      {
          $value = $value ?? $this->request->get('source');

          return $query->where('source', '=', "$value");
      }
  }
