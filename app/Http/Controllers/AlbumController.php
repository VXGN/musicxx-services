<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormater;
use App\Models\Album;
use App\Models\Artist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dedoc\Scramble\Attributes\Response;
use Dedoc\Scramble\Attributes\Header;

#[Header(name: 'Authorization', description: 'Bearer {token}', required: true)]
class AlbumController extends Controller
{

    #[Header(name: 'Authorization', description: 'Bearer {token}', required: true)]
    #[Response(500, description: 'Failed to fetch albums', mediaType: 'application/json', type: 'error', examples: ['{"status":500,"message":"Failed to fetch albums","data":{"error":"Detailed error message"}}'])]
    #[Response(200, description: 'Success', mediaType: 'application/json', type: 'album', examples: ['{"status":200,"message":"Success","data":[{"id":1,"title":"Album Title","artist":{"id":1,"name":"Artist Name"},"songs":[{"id":1,"title":"Song Title"}]}]}'])]
    public function index()
    {
        try {
            $albums = Album::with(['artist', 'songs'])->get();

            return ApiFormater::createJSON(200, 'Success', $albums);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch albums', ['error' => $e->getMessage()]);
        }
    }

    #[Header(name: 'Authorization', description: 'Bearer {token}', required: true)]
    #[Response(500, description: 'Failed to fetch album', mediaType: 'application/json', type: 'error', examples: ['{"status":500,"message":"Failed to fetch album","data":{"error":"Detailed error message"}}'])]
    #[Response(404, description: 'Album not found', mediaType: 'application/json', type: 'error', examples: ['{"status":404,"message":"Album not found","data":[] }'])]
    #[Response(200, description: 'Success', mediaType: 'application/json', type: 'album', examples: ['{"status":200,"message":"Success","data":{"id":1,"title":"Album Title","artist":{"id":1,"name":"Artist Name"},"songs":[{"id":1,"title":"Song Title"}]}}'])]
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

    #[Header(name: 'Authorization', description: 'Bearer {token}', required: true)]
    #[Response(403,
    description: 'Only publishers can create albums',
    mediaType: 'application/json', type: 'error',
    examples: ['{"status":403,"message":"Only publishers can create albums","data":[] }',
    '{"status":403,"message":"You need to create an artist profile first","data":[] }'])]
    #[Response(201, description: 'Album created successfully', mediaType: 'application/json', type: 'album', examples: ['{"status":201,"message":"Album created successfully","data":{"id":1,"title":"Album Title","artist":{"id":1,"name":"Artist Name"},"songs":[]}}'])]
    #[Response(422, description: 'Validation failed', mediaType: 'application/json', type: 'error', examples: ['{"status":422,"message":"Validation failed","data":{"title":["The title field is required."]}}'])]
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

    #[Header(name: 'Authorization', description: 'Bearer {token}', required: true)]
    #[Response(404, description: 'Album not found', mediaType: 'application/json', type: 'error', examples: ['{"status":404,"message":"Album not found","data":[] }'])]
    #[Response(403,
    description: 'Only publishers can update albums',
    mediaType: 'application/json', type: 'error',
    examples: ['{"status":403,"message":"Only publishers can update albums","data":[] }',
    '{"status":403,"message":"You can only update your own albums","data":[] }'])]
    #[Response(422, description: 'Validation failed', mediaType: 'application/json', type: 'error', examples: ['{"status":422,"message":"Validation failed","data":{"title":["The title field is required."]}}'])]
    #[Response(200, description: 'Album updated successfully', mediaType: 'application/json', type: 'album', examples: ['{"status":200,"message":"Album updated successfully","data":{"id":1,"title":"Updated Album Title","artist":{"id":1,"name":"Artist Name"},"songs":[{"id":1,"title":"Song Title"}]}}'])]
    #[Response(500, description: 'Failed to update album', mediaType: 'application/json', type: 'error', examples: ['{"status":500,"message":"Failed to update album","data":{"error":"Detailed error message"}}'])]
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

    #[Header(name: 'Authorization', description: 'Bearer {token}', required: true)]
    #[Response(404, description: 'Album not found', mediaType: 'application/json', type: 'error', examples: ['{"status":404,"message":"Album not found","data":[] }'] )]
    #[Response(403,
    description: 'Only publishers can delete albums',
    mediaType: 'application/json', type: 'error',
    examples: ['{"status":403,"message":"Only publishers can delete albums","data":[] }',
    '{"status":403,"message":"You can only delete your own albums","data":[] }'])]
    #[Response(200, description: 'Album deleted successfully', mediaType: 'application/json', type: 'success', examples: ['{"status":200,"message":"Album deleted successfully","data":[] }'])]
    #[Response(500, description: 'Failed to delete album', mediaType: 'application/json', type: 'error', examples: ['{"status":500,"message":"Failed to delete album","data":{"error":"Detailed error message"}}'])]
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
