<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;

  class KeywordFilter extends Filter
  {
      public function shouldApply(): bool
      {
          return !empty($this->request->get('keyword'));
      }

      public function apply(Builder $query, $value = null): Builder
      {
          $keyword = $value ?? $this->request->get('keyword');

          return $query->where('title', 'like', "%$keyword%");
      }
  }
