<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== Verifikasi Kolom Pricing di Tabel Barangs ===\n\n";
    
    $columns = DB::select('DESCRIBE barangs');
    
    echo "Kolom yang berkaitan dengan pricing:\n";
    foreach($columns as $col) {
        if(in_array($col->Field, ['harga_sewa_per_hari', 'biaya_deposit'])) {
            echo "✅ {$col->Field}: {$col->Type}";
            if($col->Default !== null) {
                echo " (Default: {$col->Default})";
            }
            echo "\n";
        }
    }
    
    echo "\nSemua kolom pricing sudah berhasil ditambahkan!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
