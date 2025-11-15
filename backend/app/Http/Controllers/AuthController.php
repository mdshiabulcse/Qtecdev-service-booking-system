<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role
            ],
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details, please try again'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Create token with role-based abilities
        $abilities = [];
        if ($user->role === 'admin') {
            $abilities = ['admin'];
        } elseif ($user->role === 'teacher') {
            $abilities = ['teacher'];
        } elseif ($user->role === 'parent') {
            $abilities = ['parent'];
        }

        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role
            ],
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $bearerToken = $request->bearerToken();
        if ($request->user()) {
            $request->user()->tokens()->where('token', hash('sha256', $bearerToken))->delete();
        }

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function checkAuth(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role
            ],
        ]);
    }
}
