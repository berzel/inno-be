<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  class DateFilter extends Filter
  {
      private mixed $from;

      private mixed $to;

      public function __construct(Request $request)
      {
          parent::__construct($request);
          $this->from = $this->request->get('from');
          $this->to = $this->request->get('to');
      }

      public function shouldApply(): bool
      {
          return !empty($this->from) || !empty($this->request->to);
      }

      public function apply(Builder $query, $value = null): Builder
      {
          if (!empty($this->from)) {
              $query->where('created_at', '>=', $this->from);
          }

          if (!empty($this->to)) {
              $query->where('created_at', '<=', $this->to);
          }

          return $query;
      }
  }
