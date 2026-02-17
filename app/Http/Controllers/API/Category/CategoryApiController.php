<?php

namespace App\Http\Controllers\API\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Cache;

class CategoryApiController extends Controller
{
    use ApiResponse;

    public function categoryList()
    {
        $cacheKey = 'categories_all';

        $categories = Cache::remember($cacheKey, 60 * 60, function () {
            return Category::latest()->get();
        });

        return $this->sendResponse($categories, __('Categories Fetched Successfully'));
    }
}
