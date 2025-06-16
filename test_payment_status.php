<?php

/**
 * Script untuk testing update status pembayaran
 * Jalankan dengan: php test_payment_status.php
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== Test Payment Status Update ===\n\n";

try {
    // Cari peminjaman dengan status disetujui dan belum dibayar
    $peminjaman = Peminjaman::where('status', 'disetujui')
        ->where('payment_status', '!=', 'paid')
        ->first();

    if (!$peminjaman) {
        echo "❌ Tidak ada peminjaman dengan status 'disetujui' yang belum dibayar\n";
        echo "Membuat dummy data untuk testing...\n\n";
        
        // Buat data dummy jika tidak ada
        $peminjaman = Peminjaman::first();
        if ($peminjaman) {
            $peminjaman->update([
                'status' => 'disetujui',
                'payment_status' => 'pending',
                'paid_at' => null
            ]);
            echo "✅ Data dummy dibuat untuk testing\n";
        } else {
            echo "❌ Tidak ada data peminjaman sama sekali\n";
            exit(1);
        }
    }

    echo "📋 Testing dengan Peminjaman:\n";
    echo "   - ID: {$peminjaman->id}\n";
    echo "   - Kode: {$peminjaman->kode_peminjaman}\n";
    echo "   - Status: {$peminjaman->status}\n";
    echo "   - Payment Status: {$peminjaman->payment_status}\n";
    echo "   - Total Pembayaran: {$peminjaman->formatted_total_pembayaran}\n\n";

    // Simulate successful payment callback
    echo "🔄 Simulasi callback pembayaran berhasil...\n";
    
    $beforeUpdate = [
        'payment_status' => $peminjaman->payment_status,
        'paid_at' => $peminjaman->paid_at
    ];

    // Update status pembayaran menjadi paid
    $updated = $peminjaman->update([
        'payment_status' => Peminjaman::PAYMENT_PAID,
        'paid_at' => now(),
        'midtrans_response' => [
            'transaction_status' => 'settlement',
            'order_id' => $peminjaman->midtrans_order_id ?? 'test-' . $peminjaman->id,
            'gross_amount' => (string)$peminjaman->total_pembayaran,
            'payment_type' => 'bank_transfer',
            'transaction_time' => now()->toISOString(),
            'status_code' => '200'
        ]
    ]);

    if ($updated) {
        $peminjaman->refresh();
        
        echo "✅ Update berhasil!\n\n";
        echo "📊 Status sebelum update:\n";
        echo "   - Payment Status: {$beforeUpdate['payment_status']}\n";
        echo "   - Paid At: " . ($beforeUpdate['paid_at'] ? $beforeUpdate['paid_at'] : 'null') . "\n\n";
        
        echo "📊 Status setelah update:\n";
        echo "   - Payment Status: {$peminjaman->payment_status}\n";
        echo "   - Paid At: {$peminjaman->paid_at}\n";
        echo "   - Midtrans Response: " . (is_array($peminjaman->midtrans_response) ? 'Ada' : 'Kosong') . "\n\n";

        // Verify the changes
        if ($peminjaman->payment_status === 'paid' && $peminjaman->paid_at) {
            echo "✅ SUKSES: Status pembayaran berhasil diubah menjadi 'paid'\n";
            echo "✅ SUKSES: Timestamp paid_at berhasil diset\n";
            
            // Test formatted total pembayaran
            if ($peminjaman->formatted_total_pembayaran) {
                echo "✅ SUKSES: Total pembayaran terformat: {$peminjaman->formatted_total_pembayaran}\n";
            }
            
        } else {
            echo "❌ GAGAL: Status pembayaran tidak berubah dengan benar\n";
        }

    } else {
        echo "❌ GAGAL: Update tidak berhasil\n";
    }

    echo "\n=== Test Selesai ===\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
