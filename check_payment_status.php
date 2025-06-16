<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Peminjaman;

echo "=== CHECKING PAYMENT STATUS ===\n";

$peminjamans = Peminjaman::select('id', 'kode_peminjaman', 'payment_status', 'midtrans_order_id', 'paid_at')
    ->whereNotNull('midtrans_order_id')
    ->get();

if ($peminjamans->isEmpty()) {
    echo "No peminjamans with midtrans_order_id found.\n";
} else {
    foreach ($peminjamans as $p) {
        echo "ID: {$p->id}\n";
        echo "Code: {$p->kode_peminjaman}\n";
        echo "Status: {$p->payment_status}\n";
        echo "Order ID: {$p->midtrans_order_id}\n";
        echo "Paid At: {$p->paid_at}\n";
        echo "---\n";
    }
}

echo "=== DONE ===\n";
