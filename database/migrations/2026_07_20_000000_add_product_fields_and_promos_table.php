<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add fields to barang table if they don't exist
        Schema::table('barang', function (Blueprint $table) {
            if (!Schema::hasColumn('barang', 'gambar')) {
                $table->string('gambar')->nullable()->after('satuan_barang');
            }
            if (!Schema::hasColumn('barang', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('gambar');
            }
        });

        // Create promo table
        Schema::create('promo', function (Blueprint $table) {
            $table->id('id_promo');
            $table->string('nama_promo');
            $table->string('tipe_promo'); // diskon_persen, potongan_harga
            $table->decimal('nilai_promo', 15, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('id_barang')->nullable(); // Target a specific product. Null means applies to all products.
            $table->tinyInteger('status_promo')->default(1); // 1 = Active, 0 = Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo');
        
        Schema::table('barang', function (Blueprint $table) {
            if (Schema::hasColumn('barang', 'gambar')) {
                $table->dropColumn('gambar');
            }
            if (Schema::hasColumn('barang', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
        });
    }
};
