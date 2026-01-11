<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Playlist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlaylistController extends Controller
{
    public function index()
    {
        try {
            $playlists = Playlist::with(['user', 'songs'])->get();

            return ApiFormater::createJSON(200, 'Success', $playlists);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch playlists', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $playlist = Playlist::with(['user', 'songs'])->find($id);

            if (!$playlist) {
                return ApiFormater::createJSON(404, 'Playlist not found');
            }

            return ApiFormater::createJSON(200, 'Success', $playlist);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch playlist', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'song_id' => 'required|exists:songs,id',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $playlist = Playlist::create([
                'name' => $request->name,
                'user_id' => $user->id,
            ]);

            $playlist->songs()->attach($request->song_id);

            return ApiFormater::createJSON(201, 'Playlist created successfully', $playlist->load(['user', 'songs']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to create playlist', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = auth('api')->user();
            $playlist = Playlist::find($id);

            if (!$playlist) {
                return ApiFormater::createJSON(404, 'Playlist not found');
            }

            if ($playlist->user_id !== $user->id) {
                return ApiFormater::createJSON(403, 'You can only update your own playlists');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $playlist->fill($request->only(['name']));
            $playlist->save();
            $playlist->refresh();

            return ApiFormater::createJSON(200, 'Playlist updated successfully', $playlist->load(['user', 'songs']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update playlist', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth('api')->user();
            $playlist = Playlist::find($id);

            if (!$playlist) {
                return ApiFormater::createJSON(404, 'Playlist not found');
            }

            if ($playlist->user_id !== $user->id) {
                return ApiFormater::createJSON(403, 'You can only delete your own playlists');
            }

            $playlist->delete();

            return ApiFormater::createJSON(200, 'Playlist deleted successfully');
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to delete playlist', ['error' => $e->getMessage()]);
        }
    }

    public function addSong(Request $request, $id)
    {
        try {
            $user = auth('api')->user();
            $playlist = Playlist::find($id);

            if (!$playlist) {
                return ApiFormater::createJSON(404, 'Playlist not found');
            }

            if ($playlist->user_id !== $user->id) {
                return ApiFormater::createJSON(403, 'You can only add songs to your own playlists');
            }

            $validator = Validator::make($request->all(), [
                'song_id' => 'required|exists:songs,id',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            if ($playlist->songs()->where('song_id', $request->song_id)->exists()) {
                return ApiFormater::createJSON(409, 'Song already in playlist');
            }

            $playlist->songs()->attach($request->song_id);

            return ApiFormater::createJSON(200, 'Song added to playlist successfully', $playlist->load(['user', 'songs']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to add song to playlist', ['error' => $e->getMessage()]);
        }
    }

    public function removeSong($id, $songId)
    {
        try {
            $user = auth('api')->user();
            $playlist = Playlist::find($id);

            if (!$playlist) {
                return ApiFormater::createJSON(404, 'Playlist not found');
            }
           
            if ($playlist->user_id !== $user->id) {
                return ApiFormater::createJSON(403, 'You can only remove songs from your own playlists');
            }

            if (!$playlist->songs()->where('song_id', $songId)->exists()) {
                return ApiFormater::createJSON(404, 'Song not found in playlist');
            }

            $playlist->songs()->detach($songId);

            return ApiFormater::createJSON(200, 'Song removed from playlist successfully', $playlist->load(['user', 'songs']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to remove song from playlist', ['error' => $e->getMessage()]);
        }
    }
}
