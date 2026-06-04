<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        // Tambahkan image_url ke setiap produk
        $products->each(function($product) {
            $product->image_url = $product->image
                ? asset('storage/' . $product->image)
                : null;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Data produk berhasil diambil'
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post') && $request->hasHeader('Content-Type')) {
            $contentType = $request->header('Content-Type');
            if (str_contains($contentType, 'multipart/form-data')) {
                // Parse multipart manually
                $data = $request->all();
                // ... handle multipart
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'descriptions' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // ✅ Validasi gambar
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->descriptions = $request->descriptions;
        $product->price = $request->price;
        $product->stock = $request->stock;

        // ✅ Upload gambar jika ada
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        // ✅ Tambahkan image_url ke response
        $product->image_url = $product->image
            ? asset('storage/' . $product->image)
            : null;

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product created successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // ✅ Tambahkan image_url
        $product->image_url = $product->image
            ? asset('storage/' . $product->image)
            : null;

        return response()->json([
            'success' => true,
            'data' => $product
        ], Response::HTTP_OK);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'descriptions' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // ✅ Validasi gambar
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // ✅ Update field teks
        if ($request->has('name')) $product->name = $request->name;
        if ($request->has('descriptions')) $product->descriptions = $request->descriptions;
        if ($request->has('price')) $product->price = $request->price;
        if ($request->has('stock')) $product->stock = $request->stock;

        // ✅ Upload gambar baru (hapus gambar lama)
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        // ✅ Tambahkan image_url ke response
        $product->image_url = $product->image
            ? asset('storage/' . $product->image)
            : null;

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product updated successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // ✅ Hapus file gambar jika ada
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Reduce product stock (custom method untuk Flutter)
     */
    public function reduceStock(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $quantity = $request->quantity;

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock
            ], Response::HTTP_BAD_REQUEST);
        }

        $product->stock -= $quantity;
        $product->save();

        $product->image_url = $product->image
            ? asset('storage/' . $product->image)
            : null;

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => "Stock reduced by $quantity"
        ], Response::HTTP_OK);
    }

    // Upload Image
    public function uploadImage(Request $request, $id)
    {
        try {
            Log::info('Upload image called', [
                'product_id' => $id,
                'has_file' => $request->hasFile('image'),
                'all_files' => $request->allFiles(),
                'all_input' => $request->all()
            ]);

            $product = Product::find($id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            // 🔥 Cek apakah ada file yang dikirim
            if (!$request->hasFile('image')) {
                return response()->json([
                    'message' => 'No image file found',
                    'received' => $request->allFiles()
                ], 400);
            }

            $file = $request->file('image');

            // 🔥 Validasi manual
            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'Uploaded file is not valid',
                    'error' => $file->getError()
                ], 400);
            }

            // 🔥 Validasi ukuran dan tipe
            $validator = Validator::make(['image' => $file], [
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Hapus gambar lama
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // // Simpan dengan cara manual
            $destinationPath = storage_path('app/public/products');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = $originalName . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);

            $product->image = 'products/' . $filename;
            $product->save();

            return response()->json([
                'success' => true,
                'image_url' => asset('storage/products/' . $filename)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Upload image error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}