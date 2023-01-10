<?php

namespace App\Http\Controllers;

use App\Helpers\apiResponse;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return apiResponse::success(CategoryResource::collection($categories), 'Category Fetch Success');
    }
}
