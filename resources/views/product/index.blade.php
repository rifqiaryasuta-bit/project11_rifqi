<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Nunito', sans-serif;
        }
        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e3e6f0;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
        }
        .table th {
            background-color: #f8f9fc;
            color: #4e73df;
            font-weight: 700;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
        }
        .product-img {
            width: 80px;
            height: auto;
            border-radius: 5px;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light py-3 mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel" width="30" class="me-2">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link text-muted px-3" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active fw-semibold text-dark px-3" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Master Data
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm mt-2" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item fw-bold text-primary" href="{{ route('product.index') }}">Product</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-muted px-2" href="#" role="button" id="dropdownAdmin" data-bs-toggle="dropdown" aria-expanded="false">
                       <img src="{{ asset('img/arya.jpeg') }}" 
     class="rounded-circle me-2" 
     alt="Admin" 
     width="32" 
     height="32" 
     style="object-fit: cover;">
                        <span class="fw-semibold text-dark me-1" style="font-size: 0.95rem;">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownAdmin">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-semibold py-2">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <h3 class="fw-bold text-dark mb-4">Data Product</h3>

        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0 text-dark">Data Product</h5>
                <div>
                    <a href="{{ route('product.create') }}" class="btn btn-success fw-semibold me-2 px-3 py-2" style="border-radius: 8px;">
                        <i class="fas fa-plus me-1"></i> Tambah Product
                    </a>
                    <a href="{{ route('product.cetakPdf') }}" class="btn btn-primary fw-semibold px-3 py-2" style="border-radius: 8px;" target="_blank">
                        <i class="fas fa-print me-1"></i> Cetak PDF
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">Nama</th>
                            <th style="width: 15%;">Gambar</th>
                            <th style="width: 25%;">Deskripsi</th>
                            <th style="width: 10%;">Harga</th>
                            <th style="width: 10%;">Stock</th>
                            <th style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $key => $product)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="text-start">{{ $product->name }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="{{ $product->name }}">
                                    @else
                                        <img src="https://via.placeholder.com/150" class="product-img" alt="No Image">
                                    @endif
                                </td>
                                <td class="text-start">{{ $product->descriptions }}</td>
                                <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('product.show', $product->id) }}" class="btn btn-secondary btn-sm px-3">Lihat</a>
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-warning btn-sm text-white px-3">Edit</a>
                                        <form action="{{ route('product.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm px-3">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Data Product belum tersedia. Silakan klik tombol Tambah Product.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>