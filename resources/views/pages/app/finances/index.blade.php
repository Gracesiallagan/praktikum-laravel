@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">📒 Catatan Keuangan</h3>
        <a href="#" class="btn btn-primary">
            + Tambah Catatan
        </a>
    </div>

    <div class="alert alert-info">
        Selamat datang di halaman Catatan Keuangan! 🎉 <br>
        Fitur ini akan menampilkan data pemasukan & pengeluaran kamu.
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p>Belum ada data keuangan untuk saat ini.</p>
            <p class="text-muted mb-0">Kamu bisa menambahkan catatan baru dengan tombol di atas.</p>
        </div>
    </div>
</div>
@endsection