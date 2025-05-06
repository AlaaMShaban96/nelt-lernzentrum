<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();

        return response()->json($settings);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|email',
            'phone_numbers' => 'nullable|array',
            'phone_numbers.*' => 'string|max:20',
            'location' => 'nullable|string',
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'welcome_title' => 'nullable|string|max:255',
            'welcome_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['logo', 'cover_photo']);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('settings', 'public');
            $data['logo'] = $logoPath;
        }

        if ($request->hasFile('cover_photo')) {
            $coverPhotoPath = $request->file('cover_photo')->store('settings', 'public');
            $data['cover_photo'] = $coverPhotoPath;
        }

        $settings = Setting::first();
        if ($settings) {
            $settings->update($data);
        } else {
            $settings = Setting::create($data);
        }

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        return $this->store($request);
    }
}
