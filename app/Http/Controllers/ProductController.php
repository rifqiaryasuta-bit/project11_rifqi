<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Menampilkan halaman daftar produk.
     */
    public function index()
    {
        $products = Product::all(); 
        return view('product.index', compact('products'));
    }

    /**
     * Menampilkan form untuk tambah produk baru.
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Menampilkan halaman khusus untuk cetak PDF / Print.
     */
    public function cetakPdf()
    {
        $products = Product::all();
        return view('product.cetak', compact('products'));
    }

    /**
     * Menyimpan data produk baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'descriptions' => 'required',
            'price'        => 'required|numeric',
            'stock'        => 'required|integer',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name'         => $request->name,
            'descriptions' => $request->descriptions,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'image'        => $imagePath,
        ]);

        return redirect()->route('product.index')->with('success', 'Product berhasil ditambahkan!');
    }

    /**
     * TOMBOL LIHAT: Menampilkan detail satu produk.
     */
    public function show(Product $product)
    {
        return view('product.show', compact('product'));
    }

    /**
     * TOMBOL EDIT: Menampilkan form edit data produk.
     */
    public function edit(Product $product)
    {
        return view('product.edit', compact('product'));
    }

    /**
     * PROSES UPDATE: Memperbarui data produk di database.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'descriptions' => 'required',
            'price'        => 'required|numeric',
            'stock'        => 'required|integer',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Jika user mengunggah gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage agar tidak memenuhi memori
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Simpan gambar baru
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name'         => $request->name,
            'descriptions' => $request->descriptions,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'image'        => $product->image,
        ]);

        return redirect()->route('product.index')->with('success', 'Product berhasil diperbarui!');
    }

    /**
     * TOMBOL HAPUS: Menghapus data produk sekaligus file gambarnya.
     */
    public function destroy(Product $product)
    {
        // Hapus file gambar dari folder storage jika ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Hapus data dari tabel database
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product berhasil dihapus!');
    }

    /**
     * Fungsi khusus untuk merespon Request API dari Postman
     */
    public function apiIndex()
    {
        // Mengambil semua data produk dari database
        $products = Product::all()->map(function ($product) {
            return [
                'id'           => $product->id,
                'name'         => $product->name,
                'image'        => $product->image,
                'descriptions' => $product->descriptions,
                'price'        => $product->price,
                'stock'        => $product->stock,
                'created_at'   => $product->created_at,
                'updated_at'   => $product->updated_at,
                // Membuat link gambar utuh (URL) secara otomatis
                'image_url'    => $product->image ? asset('storage/' . $product->image) : asset('img/no-image.png'),
            ];
        }); // <-- DI SINI TADI KURANG TANDA PENUTUP );

        // Mengembalikan response JSON dengan status 200 OK
        return response()->json([
            'success' => true,
            'data'    => $products
        ], 200);
    }
}