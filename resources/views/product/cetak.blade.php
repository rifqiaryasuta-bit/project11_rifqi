<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Arial', sans-serif;
            color: #000;
        }
        .table th {
            background-color: #f8f9fc !important;
            color: #000 !important;
            font-weight: bold;
            border: 1px solid #000 !important;
        }
        .table td {
            border: 1px solid #000 !important;
            vertical-align: middle;
        }
        .product-img {
            width: 70px;
            height: auto;
            border-radius: 3px;
            object-fit: cover;
        }
        /* Style otomatis memicu pop-up print saat halaman terbuka */
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-uppercase">Laporan Data Product</h2>
            <p class="text-muted mb-0">Dicetak pada tanggal: {{ date('d-m-Y H:i') }}</p>
            <hr style="border: 2px solid #000; opacity: 1;">
        </div>

        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Product</th>
                    <th style="width: 20%;">Gambar</th>
                    <th style="width: 30%;">Deskripsi</th>
                    <th style="width: 15%;">Harga</th>
                    <th style="width: 10%;">Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="text-start fw-semibold">{{ $product->name }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="Product">
                            @else
                                <span class="text-muted small">No Image</span>
                            @endif
                        </td>
                        <td class="text-start">{{ $product->descriptions }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->stock }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-3">Tidak ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>