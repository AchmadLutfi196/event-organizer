<?php

// Test script untuk memeriksa harga barang
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Barang;

echo "=== TESTING HARGA BARANG ===\n";

$barangs = Barang::select('id', 'nama', 'harga_sewa_per_hari', 'biaya_deposit')->get();

foreach ($barangs as $barang) {
    echo "ID: {$barang->id}\n";
    echo "Nama: {$barang->nama}\n";
    echo "Harga Sewa: {$barang->harga_sewa_per_hari}\n";
    echo "Deposit: {$barang->biaya_deposit}\n";
    echo "Formatted Sewa: {$barang->formatted_harga_sewa}\n";
    echo "Formatted Deposit: {$barang->formatted_deposit}\n";
    echo "---\n";
}

echo "=== SELESAI ===\n";
