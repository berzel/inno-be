<?php

  namespace App\Filters;

  use Illuminate\Database\Eloquent\Builder;
  use Illuminate\Http\Request;

  class PersonalizedFeedFilter extends Filter
  {

      public function __construct(
        Request                           $request,
        private readonly CategoriesFilter $categoriesFilter,
        private readonly SourcesFilter $sourcesFilter
      ) {
        parent::__construct($request);
      }

      public function shouldApply(): bool
      {
          return auth('sanctum')->check() && !empty($this->request->get('personalized_feed'));
      }

      public function apply(Builder $query, $value = null): Builder
      {
        if (!$this->shouldApply()) {
          return $query;
        }

        if ($categories = auth('sanctum')->user()->preferred_categories) {
            $query = $this->categoriesFilter->apply($query, $categories);
        }

        if ($sources = auth('sanctum')->user()->preferred_sources) {
            $query = $this->sourcesFilter->apply($query, $sources);
        }

        return $query;
      }
  }
