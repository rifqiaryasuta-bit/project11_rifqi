<?php

use App\Http\Controllers\ProductController; // Memastikan namespace controller-mu tepat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route API untuk pembuktian proyek sukses di Postman (Sederhana)
Route::get('/check', function () {
    return response()->json([
        'message' => 'API route works'
    ], 200);
});

// Route untuk mengambil data produk asli dalam bentuk JSON (Sesuai slide dosen)
Route::get('/products-json', [ProductController::class, 'apiIndex']);

// Route CRUD API bawaan (apiResource)
Route::apiResource('products', ProductController::class);

Route::post('products/{id}/upload-image', [ProductController::class, 'uploadImage']);

// Route custom untuk mengurangi stok
Route::patch('/products/{id}/reduce-stock', [ProductController::class, 'reduceStock']);

// Akses gambar via API
Route::get('/image/{filename}', function ($filename) {
    $path = storage_path('app/public/products/' . $filename);

    if(!file_exists($path)) {
        return response()->json(['error' => 'Image not found'], 404);
    }

    return response()->file($path, [
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('filename', '.*');