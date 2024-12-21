<?php

  namespace App\Filters;


  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  abstract class Filter
  {
      public function __construct(
          protected Request $request
      ) {

      }

      public abstract function shouldApply(): bool;

      public abstract function apply(Builder $query, $value = null) : Builder;
  }
