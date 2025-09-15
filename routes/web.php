<?php

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaUploadService;



Route::get('/', function () {
    return view('welcome');
});



// Route::get('/upload', function () {
//     return view('uploads.upload');
// });
// Route::post('/upload', function (Request $request) {
//     $request->validate([
//         'file' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov|max:10240',
//     ]);

//     $path = Storage::disk('cloudinary')->put('tests', $request->file('file'));
//     $url  = Storage::disk('cloudinary')->url($path);

//     return back()->with('uploaded_url', $url);
// })->name('upload');




Route::get('/upload-test', function () {
    return view('uploads.upload'); // we'll make a simple form view
});

Route::post('/test-media-upload', function (Request $request) {
    try {
        $mediaService = new MediaUploadService();

        if ($request->hasFile('image')) {
            $result = $mediaService->uploadCover($request->file('image'), 999);
            return response()->json([
                'success' => true,
                'message' => 'Cover uploaded successfully',
                'data' => $result,
            ]);
        }

        if ($request->hasFile('video')) {
            $result = $mediaService->uploadVideo($request->file('video'), 999, 999);
            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully',
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file provided',
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage(),
        ], 500);
    }
});
