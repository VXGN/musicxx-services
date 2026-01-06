<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * REGISTER USER
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'nullable|in:listener,publisher',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'] ?? 'listener',
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Register berhasil',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    /**
     * LOGIN USER
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah'],
            ]);
        }

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => auth()->user(),
        ]);
    }

    /**
     * LOGOUT (Invalidate Token)
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * REFRESH TOKEN
     */
    public function refresh()
    {
        return response()->json([
            'token' => JWTAuth::parseToken()->refresh(),
        ]);
    }

    /**
     * GET AUTHENTICATED USER
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
}
