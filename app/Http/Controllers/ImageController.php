<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $all =  $request->all();
            $request->validate(['image' => 'required|image|max:51200']); // 50MB max size
            
            $image = $request->file('image');
            $filename = Storage::disk('images')->putFile('', $image);

            $image = Image::create([
                'filename' => $filename,
                'name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'alt' => $image->getClientOriginalName(),
                'mimeType' => $image->getMimeType() ,
                'created_by' => $request->user()->id,
            ]);

            return response()->json($image, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Der skete en kritisk fejl'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $image = Image::findOrFail($id);
            return response()->file(
                $image->path, 
                ['Content-Type' => $image->mimeType]
            );
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Image not found'], 404);
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $image = Image::findOrFail($id);
            DB::beginTransaction();
            $deleted = Storage::disk('images')->delete($image->path);
            if ($deleted) {
                $image->delete();
                DB::commit();
                return response()->json(['message' => 'Image deleted successfully'], 200);
            } else {
                throw new \Exception('Image file could not be deleted');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while deleting the image'], 500);
        }
    }
}
