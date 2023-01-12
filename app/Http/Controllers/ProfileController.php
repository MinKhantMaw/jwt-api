<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Helpers\apiResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProfileResource;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();
        return apiResponse::success(new ProfileResource($user));
    }

    public function posts(Request $request)
    {
        $query = Post::orderByDesc('created_at')->where('user_id', auth()->user()->id);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like ', '%' . $request->search . '%')->orWhere('description', 'like ', '%' . $request->search . '%');
            });
        }
        $posts = $query->paginate(10);
        return apiResponse::success(PostResource::collection($posts), 'Post List Fetch Success');
        // return PostResource::collection($posts)->additional(['message' => 'success']);
    }
}
