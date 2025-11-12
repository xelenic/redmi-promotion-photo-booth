<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        $publicUrl = Storage::disk('public')->url($path);

        return response()->json([
            'success' => true,
            'message' => 'Photo saved successfully',
            'photo' => $photo,
            'photo_url' => $publicUrl,
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

    public function generateQrCode(Request $request)
    {
        $request->validate([
            'data' => 'required|string',
            'size' => 'nullable|integer|min:50|max:1000',
        ]);

        $data = $request->input('data');
        $size = $request->input('size', 320);

        // Generate QR code as PNG
        $qrCode = QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($data);

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
