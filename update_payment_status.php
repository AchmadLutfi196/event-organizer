<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Peminjaman;
use Midtrans\Config;
use Midtrans\Transaction;

// Set Midtrans configuration
Config::$serverKey = config('midtrans.server_key');
Config::$isProduction = config('midtrans.is_production');

echo "=== CHECKING AND UPDATING PAYMENT STATUS ===\n";

$peminjamans = Peminjaman::where('payment_status', 'pending')
    ->whereNotNull('midtrans_order_id')
    ->get();

foreach ($peminjamans as $peminjaman) {
    echo "Checking Order ID: {$peminjaman->midtrans_order_id}\n";
    
    try {
        // Check status from Midtrans
        $status = Transaction::status($peminjaman->midtrans_order_id);
        
        echo "Midtrans Status: {$status->transaction_status}\n";
        
        // Update status based on Midtrans response
        if (in_array($status->transaction_status, ['capture', 'settlement'])) {
            $peminjaman->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'midtrans_response' => json_encode($status)
            ]);
            echo "✅ Updated to PAID\n";
        } elseif (in_array($status->transaction_status, ['deny', 'expire', 'cancel'])) {
            $peminjaman->update([
                'payment_status' => 'failed',
                'midtrans_response' => json_encode($status)
            ]);
            echo "❌ Updated to FAILED\n";
        } else {
            echo "⏳ Remains PENDING\n";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    echo "---\n";
}

echo "=== DONE ===\n";
