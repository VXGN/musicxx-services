<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:listener,publisher',
            'password' => 'required|string|min:6',
        ]);
        
        if ($validator->fails()) {
            return ApiFormater::createJSON(422, 'Validation Error', $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token, 'User registered successfully');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiFormater::createJSON(422, 'Validation Error', $validator->errors());
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return ApiFormater::createJSON(401, 'Unauthorized', ['error' => 'Invalid credentials']);
        }

        return $this->respondWithToken($token, 'Login successful');
    }

    public function me()
    {
        return ApiFormater::createJSON(200, 'User details retrieved successfully', auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();

        return ApiFormater::createJSON(200, 'Successfully logged out', []);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh(), 'Token refreshed successfully');
    }

    protected function respondWithToken($token, $message = 'Success')
    {
        return ApiFormater::createJSON(200, $message, [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
