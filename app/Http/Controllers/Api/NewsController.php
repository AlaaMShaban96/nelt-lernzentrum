<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('photos')->latest()->get();

        // Transform the response to include full URLs for photos
        $news->transform(function ($item) {
            $item->photos->transform(function ($photo) {
                $photo->photo_url = asset('storage/'.$photo->photo_path);

                return $photo;
            });

            return $item;
        });

        return response()->json($news);
    }

    public function show($id)
    {
        $news = News::with('photos')->findOrFail($id);

        // Transform the response to include full URLs for photos
        $news->photos->transform(function ($photo) {
            $photo->photo_url = asset('storage/'.$photo->photo_path);

            return $photo;
        });

        return response()->json($news);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create the news record
        $news = News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);
        // Handle photos if any
        $photos = $request->file('photos', []);

        if (is_array($photos) && count($photos)) {
            foreach ($photos as $photo) {
                $path = $photo->store('news', 'public');

                NewsPhoto::create([
                    'news_id' => $news->id,
                    'photo_path' => $path,
                ]);
            }
        }

        return response()->json($news->load('photos'), 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $news->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        // Handle new photos if any
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('news', 'public');

                NewsPhoto::create([
                    'news_id' => $news->id,
                    'photo_path' => $path,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return response()->json($news->load('photos'));
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Delete all associated photos
        foreach ($news->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
        }

        $news->delete();

        return response()->json(null, 204);
    }
}
