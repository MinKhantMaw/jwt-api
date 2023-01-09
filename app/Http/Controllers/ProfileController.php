<?php

namespace App\Http\Controllers;

use App\Helpers\apiResponse;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();
        return apiResponse::success(new ProfileResource($user));
    }
}
