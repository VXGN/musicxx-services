<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Artist;
use App\Models\Song;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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
            $user = auth('api')->user();

            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can create songs');
            }

            // Get the artist profile linked to this user
            $artist = Artist::where('user_id', $user->id)->first();

            if (!$artist) {
                return ApiFormater::createJSON(403, 'You need to create an artist profile first');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'album_id' => 'nullable|exists:albums,id',
                'duration' => 'nullable|integer|min:0',
                'file' => 'required|file|mimes:mp3,wav,flac|max:5120',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            // If album_id is provided, verify it belongs to the artist
            if ($request->album_id) {
                $albumBelongsToArtist = $artist->albums()->where('id', $request->album_id)->exists();
                if (!$albumBelongsToArtist) {
                    return ApiFormater::createJSON(403, 'The album does not belong to your artist profile');
                }
            }

            // up 
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $bucket = 'Music';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
                'Content-Type'  => $file->getMimeType(),
            ])->withBody(
                file_get_contents($file->getRealPath()),
                $file->getMimeType()
            )->post(
                env('SUPABASE_URL') . "/storage/v1/object/$bucket/$fileName"
            );

            if ($response->failed()) {
                return ApiFormater::createJSON(500, 'Failed to upload music file', [
                    'error' => $response->body()
                ]);
            }

            $fileUrl = env('SUPABASE_URL') . "/storage/v1/object/public/$bucket/$fileName";

            $song = Song::create([
                'title' => $request->title,
                'artist_id' => $artist->id,
                'album_id' => $request->album_id,
                'duration' => $request->duration,
                'file_url' => $fileUrl,
            ]);

            return ApiFormater::createJSON(201, 'Song created successfully', $song->load(['artist', 'album']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to create song', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = auth('api')->user();
            $song = Song::find($id);

            if (!$song) {
                return ApiFormater::createJSON(404, 'Song not found');
            }

            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can update songs');
            }

            $artist = Artist::where('user_id', $user->id)->first();

            if (!$artist || $song->artist_id !== $artist->id) {
                return ApiFormater::createJSON(403, 'You can only update your own songs');
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'album_id' => 'nullable|exists:albums,id',
                'duration' => 'nullable|integer|min:0',
                'file_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            // If album_id is provided, verify it belongs to the artist
            if ($request->has('album_id') && $request->album_id) {
                $albumBelongsToArtist = $artist->albums()->where('id', $request->album_id)->exists();
                if (!$albumBelongsToArtist) {
                    return ApiFormater::createJSON(403, 'The album does not belong to your artist profile');
                }
            }

            $bucket = 'Music';
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
                    'Content-Type'  => $file->getMimeType(),
                ])->withBody(
                    file_get_contents($file->getRealPath()),
                    $file->getMimeType()
                )->put(
                    env('SUPABASE_URL') . "/storage/v1/object/$bucket/$fileName"
                );
                if ($response->failed()) {
                    return ApiFormater::createJSON(500, 'Failed to upload music file', [
                        'error' => $response->body()
                    ]);
                }
                $fileUrl = env('SUPABASE_URL') . "/storage/v1/object/public/$bucket/$fileName";
                $request->merge(['file_url' => $fileUrl]);
            }
            
            $song->fill($request->only(['title', 'album_id', 'duration', 'file_url']));
            $song->save();

            return ApiFormater::createJSON(200, 'Song updated successfully', $song->fresh()->load(['artist', 'album']));
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update song', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth('api')->user();
            $song = Song::find($id);

            if (!$song) {
                return ApiFormater::createJSON(404, 'Song not found');
            }

            // Check if user is a publisher and owns this song
            if ($user->role !== 'publisher') {
                return ApiFormater::createJSON(403, 'Only publishers can delete songs');
            }

            $artist = Artist::where('user_id', $user->id)->first();

            if (!$artist || $song->artist_id !== $artist->id) {
                return ApiFormater::createJSON(403, 'You can only delete your own songs');
            }

            $bucket = 'Music';
            $filePath = parse_url($song->file_url, PHP_URL_PATH);
            $objectPath = ltrim(str_replace("/storage/v1/object/public/$bucket/", '',$filePath), '/');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            ])->delete(
                env('SUPABASE_URL') . "/storage/v1/object/$bucket/$objectPath"
            );
            if ($response->failed()) {
                return ApiFormater::createJSON(500, 'Failed to delete music file', [
                    'error' => $response->body()
                ]);
            }

            $song->delete();

            return ApiFormater::createJSON(200, 'Song deleted successfully');
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to delete song', ['error' => $e->getMessage()]);
        }
    }
}
