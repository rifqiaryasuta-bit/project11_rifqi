{{-- resources/views/product/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Produk</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- NAMA --}}
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">Nama</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $product->name) }}"
                           class="w-full border rounded p-2">
                    @error('name')
                        <small class="text-red-600 d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- GAMBAR --}}
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">Gambar</label>
                    <input type="file"
                           name="image"
                           accept="image/*"
                           class="w-full border rounded p-2">
                    @error('image')
                        <small class="text-red-600 d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- TAMPILKAN GAMBAR SAAT INI --}}
                @if ($product->image)
                    <div class="mb-4 bg-gray-50 p-3 rounded border border-gray-200 inline-block">
                        <p class="text-xs font-semibold text-gray-500 mb-2">Gambar saat ini:</p>
                        <img src="{{ asset('storage/'.$product->image) }}"
                             alt="Product Image" width="120"
                             class="rounded shadow-sm border bg-white">
                    </div>
                @endif

                {{-- DESCRIPTIONS --}}
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">Descriptions</label>
                    <textarea name="descriptions"
                              rows="4"
                              class="w-full border rounded p-2">{{ old('descriptions', $product->descriptions) }}</textarea>
                    @error('descriptions')
                        <small class="text-red-600 d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- PRICE --}}
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">Price</label>
                    <input type="text"
                           name="price"
                           value="{{ old('price', $product->price) }}"
                           class="w-full border rounded p-2">
                    @error('price')
                        <small class="text-red-600 d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- STOCK --}}
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">Stock</label>
                    <input type="number"
                           name="stock"
                           value="{{ old('stock', $product->stock) }}"
                           class="w-full border rounded p-2">
                    @error('stock')
                        <small class="text-red-600 d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- BUTTONS ACTION --}}
                <div class="mt-6 flex items-center justify-start gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow transition">
                        Update
                    </button>
                    <a href="{{ route('product.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded font-semibold transition">
                        Batal
                    </a>
                </div>
            </form>
            
        </div>
    </div>
</x-app-layout>