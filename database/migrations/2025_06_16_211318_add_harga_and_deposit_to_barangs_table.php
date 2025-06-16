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
        Schema::table('barangs', function (Blueprint $table) {
            // Menambahkan kolom harga sewa per hari
            $table->decimal('harga_sewa_per_hari', 10, 2)->default(0)->after('stok');
            
            // Menambahkan kolom biaya deposit
            $table->decimal('biaya_deposit', 10, 2)->default(0)->after('harga_sewa_per_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['harga_sewa_per_hari', 'biaya_deposit']);
        });
    }
};
