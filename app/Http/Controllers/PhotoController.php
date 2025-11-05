<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
            'session_id' => 'nullable|string',
        ]);

        // Decode base64 image
        $image = $request->photo;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        // Generate unique filename
        $filename = 'kiosk_' . time() . '_' . Str::random(10) . '.png';
        $path = 'photos/' . date('Y/m/d') . '/' . $filename;

        // Store the image
        Storage::disk('public')->put($path, $imageData);

        // Save to database
        $photo = Photo::create([
            'filename' => $filename,
            'path' => $path,
            'session_id' => $request->session_id ?? session()->getId(),
            'metadata' => [
                'captured_at' => now()->toDateTimeString(),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Photo saved successfully',
            'photo' => $photo,
        ]);
    }

    public function index()
    {
        $photos = Photo::orderBy('created_at', 'desc')->paginate(20);
        return response()->json($photos);
    }

    public function show($id)
    {
        $photo = Photo::findOrFail($id);
        return response()->json($photo);
    }
}
