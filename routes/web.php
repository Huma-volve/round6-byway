<?php

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/', function () {
    return view('welcome');
});



Route::get('/upload', function () {
    return view('uploads.upload');
});
Route::post('/upload', function (Request $request) {
     $request->validate([
        'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov|max:10240',
    ]);

    $path = Storage::disk('cloudinary')->put('tests', $request->file('file'));
    $url  = Storage::disk('cloudinary')->url($path);

    return back()->with('uploaded_url', $url);
})->name('upload');
