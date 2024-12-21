<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  class CategoryFilter extends Filter
  {
      public function shouldApply(): bool
      {
          return !empty($this->request->get('category'));
      }

      public function apply(Builder $query, $value = null): Builder
      {
          $value = $value ?? $this->request->get('category');

          return $query->where('category', '=', "$value");
      }
  }
