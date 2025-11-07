<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('title');                        // Judul catatan
            $table->text('description')->nullable();        // Keterangan tambahan
            $table->decimal('amount', 15, 2);               // Jumlah uang
            $table->enum('type', ['income', 'expense']);    // Jenis transaksi
            $table->string('receipt_path')->nullable();     // Path bukti (gambar)
            $table->date('date');                           // Tanggal transaksi
            $table->timestamps();                           // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
