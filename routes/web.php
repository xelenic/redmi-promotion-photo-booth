<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;

Route::get('/', function () {
    return view('kiosk');
});

// Admin route for viewing photos
Route::get('/admin/photos', function () {
    return view('admin.photos');
});

// API routes for photo management
Route::post('/api/photos', [PhotoController::class, 'store']);
Route::get('/api/photos', [PhotoController::class, 'index']);
Route::get('/api/photos/{id}', [PhotoController::class, 'show']);

// QR code generation route
Route::get('/api/qrcode', [PhotoController::class, 'generateQrCode']);
