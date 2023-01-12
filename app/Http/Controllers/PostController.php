<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Media;
use App\Helpers\apiResponse;
use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user','category','image'])->orderBy('id','desc');

        if ($request->category_id) {
            $posts->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $posts->where(function ($query) use ($request) {
                $query->where('title', 'like ', '%' . $request->search . '%')->orWhere('description', 'like ', '%' . $request->search . '%');
            });
        }
        $posts = $posts->get();
        return apiResponse::success(PostResource::collection($posts), 'Post List Fetch Success');
        // return PostResource::collection($posts)->additional(['message' => 'success']);
    }

    public function create(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'category_id' => 'required',
            ],
            [
                'category_id.required' => 'The category field is required',
            ]
        );

        DB::beginTransaction();
        try {
            $file_name = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $file_name = uniqid() . '-' . date('Y-m-d H:i:s') . '.' . $file->getClientOriginalExtension();
                Storage::put('media/' . $file_name, file_get_contents($file));
            }

            $post = new Post();
            $post->title = $request->title;
            $post->description = $request->description;
            $post->category_id = $request->category_id;
            $post->user_id = auth()->user()->id;
            $post->save();

            $media = new Media();
            $media->file_name = $file_name;
            $media->file_type = 'image';
            $media->model_id = $post->id;
            $media->model_type = Post::class;
            $media->save();

            DB::commit();
            return apiResponse::success($post, 'Post created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return apiResponse::fail($e->getMessage());
        }
    }

    public function show($id)
    {
        $post = Post::where('id', $id)->get();
        return apiResponse::success(PostDetailResource::collection($post));
    }

    public function delete(Request $request)
    {
        $post = Post::findOrFail($request->id);
        return $post;
        $post->delete();
        return apiResponse::success($post, 'Post Delete Successful');
    }
}
