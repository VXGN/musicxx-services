<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Album;
use App\Models\Artist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    public function index()
    {
        try {
            $albums = Album::with(['artist', 'songs'])->get();

            return ApiFormater::createJSON(200, 'Success', $albums);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch albums', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $album = Album::with(['artist', 'songs'])->find($id);

            if (!$album) {
                return ApiFormater::createJSON(404, 'Album not found');
            }

            return ApiFormater::createJSON(200, 'Success', $album);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch album', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();

            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can create albums');
            }

            $artist = Artist::where('user_id', $user->id)->first();

            if (!$artist) {
                return ApiFormater::createJSON(403, 'You need to create an artist profile first');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'cover_image' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $album = Album::create([
                'title' => $request->title,
                'artist_id' => $artist->id,
                'cover_image' => $request->cover_image,
            ]);

            return ApiFormater::createJSON(201, 'Album created successfully', $album->load(['artist', 'songs']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to create album', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = auth('api')->user();
            $album = Album::find($id);

            if (!$album) {
                return ApiFormater::createJSON(404, 'Album not found');
            }

            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can update albums');
            }

            $artist = Artist::where('user_id', $user->id)->first();

            if (!$artist || $album->artist_id !== $artist->id) {
                return ApiFormater::createJSON(403, 'You can only update your own albums');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'cover_image' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $album->fill($request->only(['title', 'cover_image']));
            $album->save();
            $album->refresh();

            return ApiFormater::createJSON(200, 'Album updated successfully', $album->load(['artist', 'songs']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update album', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth('api')->user();
            $album = Album::find($id);

            if (!$album) {
                return ApiFormater::createJSON(404, 'Album not found');
            }

            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can delete albums');
            }

            $artist = Artist::where('user_id', $user->id)->first();

            if (!$artist || $album->artist_id !== $artist->id) {
                return ApiFormater::createJSON(403, 'You can only delete your own albums');
            }

            $album->delete();

            return ApiFormater::createJSON(200, 'Album deleted successfully');
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to delete album', ['error' => $e->getMessage()]);
        }
    }
}
