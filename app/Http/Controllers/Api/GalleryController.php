<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = Gallery::latest()->get()->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => asset('storage/gallery/'.$image->image_path),
                'created_at' => $image->created_at,
            ];
        });

        return response()->json($images);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time().'_'.$image->getClientOriginalName();

                // Store the image
                $path = $image->storeAs('public/gallery', $filename);

                // Create gallery record
                $gallery = Gallery::create([
                    'image_path' => $filename,
                ]);

                $uploadedImages[] = [
                    'id' => $gallery->id,
                    'url' => asset('storage/gallery/'.$filename),
                ];
            }

            return response()->json([
                'message' => 'Images uploaded successfully',
                'images' => $uploadedImages,
            ], 201);
        }

        return response()->json(['message' => 'No image files provided'], 400);
    }

    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);

        // Delete the image file from storage
        Storage::disk('public')->delete('gallery/'.$gallery->image_path);

        // Delete the gallery record
        $gallery->delete();

        return response()->json([
            'message' => 'Image deleted successfully'
        ], 200);
    }
}
