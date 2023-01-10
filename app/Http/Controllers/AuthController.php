<?php

namespace App\Http\Controllers;

use App\Helpers\apiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:5|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:20',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = Auth::login($user);
        return apiResponse::success([
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
        return apiResponse::success($user, 'User created successfully');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return apiResponse::fail([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ]);
        }

        $user = Auth::user();
        return apiResponse::success([
            'message' => 'Login successful',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return apiResponse::success([], 'Successfully logged out');
    }
}
