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
            $artists = Artist::with(['albums', 'songs'])->get();

            return ApiFormater::createJSON(200, 'Success', $artists);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to fetch artists', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $artist = Artist::with(['albums', 'songs'])->find($id);

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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'bio' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $artist = Artist::create($request->all());

            return ApiFormater::createJSON(201, 'Artist created successfully', $artist);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to create artist', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $artist = Artist::find($id);

            if (!$artist) {
                return ApiFormater::createJSON(404, 'Artist not found');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'bio' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiFormater::createJSON(422, 'Validation failed', $validator->errors());
            }

            $artist->update($request->all());

            return ApiFormater::createJSON(200, 'Artist updated successfully', $artist);
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to update artist', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $artist = Artist::find($id);

            if (!$artist) {
                return ApiFormater::createJSON(404, 'Artist not found');
            }

            $artist->delete();

            return ApiFormater::createJSON(200, 'Artist deleted successfully');
        } catch (Exception $e) {
            return ApiFormater::createJSON(500, 'Failed to delete artist', ['error' => $e->getMessage()]);
        }
    }
}
