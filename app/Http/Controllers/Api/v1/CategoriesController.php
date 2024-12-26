<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function top()
    {
        $categories = Category::whereHas('articles')
            ->withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->paginate();

        return CategoryResource::collection($categories);
    }
}
