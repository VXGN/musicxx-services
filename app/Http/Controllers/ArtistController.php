<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Artist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Dedoc\Scramble\Attributes\Response;

class ArtistController extends Controller
{
    #[Response(500, description: 'Failed to fetch artists', mediaType: 'application/json', type: 'error')]
    #[Response(200, description: 'Success', mediaType: 'application/json', type: 'artist')]
    public function index()
    {
        try {
            $artists = Artist::with(['albums', 'songs', 'user'])->get();

            return ApiFormater::createJSON(200, 'Success', $artists);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch artists', ['error' => $e->getMessage()]);
        }
    }

    #[Response(404, description: 'Artist not found', mediaType: 'application/json', type: 'error')]
    #[Response(200, description: 'Success', mediaType: 'application/json', type: 'artist')]
    #[Response(500, description: 'Failed to fetch artist', mediaType: 'application/json', type: 'error')]
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

    #[Response(201, description: 'Artist created successfully', mediaType: 'application/json', type: 'artist')]
    #[Response(403, description: 'Only publishers can create an artist profile', mediaType: 'application/json', type: 'error')]
    #[Response(422,
    description: 'You already have an artist profile',
    mediaType: 'application/json', type: 'error',
    examples: ['{"status":422,"message":"You already have an artist profile",
    "data":{"id":1,"user_id":2,"name":"Artist Name",
    "bio":"Artist bio","created_at":"2024-01-01T00:00:00.000000Z",
    "updated_at":"2024-01-01T00:00:00.000000Z"}}'])]
    #[Response(500, description: 'Failed to create artist', mediaType: 'application/json', type: 'error')]
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

    #[Response(404, description: 'Artist not found', mediaType: 'application/json', type: 'error')]
    #[Response(403,
    description: 'Only publishers can update artist profiles',
    mediaType: 'application/json', type: 'error',
    examples: ['{"status":403,"message":"Only publishers can update artist profiles","data":[] }',
    '{"status":403,"message":"You can only update your own artist profile","data":[] }'])]
    #[Response(422, description: 'Validation failed', mediaType: 'application/json', type: 'error')]
    #[Response(200, description: 'Artist updated successfully', mediaType: 'application/json', type: 'artist')]
    #[Response(500, description: 'Failed to update artist', mediaType: 'application/json', type: 'error')]
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

            $artist->fill($request->only(['name', 'bio']));
            $artist->save();
            $artist->refresh();

            return ApiFormater::createJSON(200, 'Artist updated successfully', $artist->load('user'));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update artist', ['error' => $e->getMessage()]);
        }
    }

    #[Response(404, description: 'Artist not found', mediaType: 'application/json', type: 'error')]
    #[Response(403,
    description: 'Only publishers can delete artist profiles',
    mediaType: 'application/json', type: 'error',
    examples: ['{"status":403,"message":"Only publishers can delete artist profiles","data":[] }',
    '{"status":403,"message":"You can only delete your own artist profile","data":[] }'])]
    #[Response(200, description: 'Artist deleted successfully', mediaType: 'application/json', type: 'success')]
    #[Response(500, description: 'Failed to delete artist', mediaType: 'application/json', type: 'error')]
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
