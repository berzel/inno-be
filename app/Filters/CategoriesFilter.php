<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  class CategoriesFilter extends Filter
  {
      public function shouldApply(): bool
      {
          return !empty($this->request->get('categories'));
      }

      public function apply(Builder $query, $value = null): Builder
      {
          $categories = $value ?? explode(',', $this->request->get('categories'));
          $query->whereIn('category', $categories);

          return $query;
      }
  }
