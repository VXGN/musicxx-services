<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication
Route::prefix("/auth")->group(function () {
    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/register", [AuthController::class, "register"]);
    Route::get("/refresh", [AuthController::class, "refresh"]);
});

Route::middleware("jwt.auth")->group(function () {

    // Authentication
    Route::delete("/auth/logout", [AuthController::class, "logout"]);
    Route::get("/auth/me", [AuthController::class, "me"]);

    Route::apiResource('songs', SongController::class);
    Route::apiResource('albums', AlbumController::class);
    Route::apiResource('artists', ArtistController::class);
    Route::apiResource('playlists', PlaylistController::class);
});
