<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Artist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtistController extends Controller
{
    public function index()
    {
        try {
            $artists = Artist::with(['albums', 'songs', 'user'])->get();

            return ApiFormater::createJSON(200, 'Success', $artists);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch artists', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $artist = Artist::with(['albums', 'songs', 'user'])->find($id);

            if (!$artist) {
                return ApiFormater::createJSON(404, 'Artist not found');
            }

            return ApiFormater::createJSON(200, 'Success', $artist);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch artist', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();

            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can create an artist profile');
            }

            $existingArtist = Artist::where('user_id', $user->id)->first();
            if ($existingArtist) {
                return ApiFormater::createJSON(422, 'You already have an artist profile', $existingArtist);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'bio' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $artist = Artist::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'bio' => $request->bio,
            ]);

            return ApiFormater::createJSON(201, 'Artist created successfully', $artist->load('user'));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to create artist', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = auth('api')->user();
            $artist = Artist::find($id);

            if (!$artist) {
                return ApiFormater::createJSON(404, 'Artist not found');
            }

            // Check if user is a publisher and owns this artist profile
            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can update artist profiles');
            }

            if ($artist->user_id !== $user->id) {
                return ApiFormater::createJSON(403, 'You can only update your own artist profile');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'bio' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $artist->update($request->only(['name', 'bio']));

            return ApiFormater::createJSON(200, 'Artist updated successfully', $artist->load('user'));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update artist', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth('api')->user();
            $artist = Artist::find($id);

            if (!$artist) {
                return ApiFormater::createJSON(404, 'Artist not found');
            }

            // Check if user is a publisher and owns this artist profile
            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can delete artist profiles');
            }

            if ($artist->user_id !== $user->id) {
                return ApiFormater::createJSON(403, 'You can only delete your own artist profile');
            }

            $artist->delete();

            return ApiFormater::createJSON(200, 'Artist deleted successfully');
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to delete artist', ['error' => $e->getMessage()]);
        }
    }
}
