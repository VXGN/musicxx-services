<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Song;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    public function index()
    {
        try {
            $songs = Song::with(['artist', 'album'])->get();

            return ApiFormater::createJSON(200, 'Success', $songs);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch songs', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $song = Song::with(['artist', 'album'])->find($id);

            if (!$song) {
                return ApiFormater::createJSON(404, 'Song not found');
            }

            return ApiFormater::createJSON(200, 'Success', $song);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch song', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'artist_id' => 'required|exists:artists,id',
                'album_id' => 'nullable|exists:albums,id',
                'duration' => 'nullable|integer|min:0',
                'file_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $song = Song::create($request->all());

            return ApiFormater::createJSON(201, 'Song created successfully', $song);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to create song', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $song = Song::find($id);

            if (!$song) {
                return ApiFormater::createJSON(404, 'Song not found');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'artist_id' => 'sometimes|required|exists:artists,id',
                'album_id' => 'nullable|exists:albums,id',
                'duration' => 'nullable|integer|min:0',
                'file_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $song->update($request->all());

            return ApiFormater::createJSON(200, 'Song updated successfully', $song);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update song', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $song = Song::find($id);

            if (!$song) {
                return ApiFormater::createJSON(404, 'Song not found');
            }

            $song->delete();

            return ApiFormater::createJSON(200, 'Song deleted successfully');
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to delete song', ['error' => $e->getMessage()]);
        }
    }
}
