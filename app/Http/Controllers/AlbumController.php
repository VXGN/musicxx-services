<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Album;
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
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'artist_id' => 'required|exists:artists,id',
                'cover_image' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $album = Album::create($request->all());

            return ApiFormater::createJSON(201, 'Album created successfully', $album);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to create album', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $album = Album::find($id);

            if (!$album) {
                return ApiFormater::createJSON(404, 'Album not found');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'artist_id' => 'sometimes|required|exists:artists,id',
                'cover_image' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $album->update($request->all());

            return ApiFormater::createJSON(200, 'Album updated successfully', $album);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update album', ['error' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {
            $album = Album::find($id);

            if (!$album) {
                return ApiFormater::createJSON(404, 'Album not found');
            }

            $album->delete();

            return ApiFormater::createJSON(200, 'Album deleted successfully');
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to delete album', ['error' => $e->getMessage()]);
        }
    }
}
