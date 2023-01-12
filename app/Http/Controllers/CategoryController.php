<?php

namespace App\Http\Controllers;

use App\Helpers\apiResponse;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return apiResponse::success(CategoryResource::collection($categories), 'Category Fetch Success');
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return apiResponse::fail($validator->errors()->all());
//            return response()->json($validator->errors()->all(), 422);
        }


       $category = new Category();
       $category->name = $request->name;
       $category->save();

        return apiResponse::success($category,'Category Created Success');
    }
}
