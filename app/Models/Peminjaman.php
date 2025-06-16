<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';
    
    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'barang_id',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'status',
        'keperluan',
        'catatan_admin',
        'total_biaya_sewa',
        'total_deposit',
        'denda_keterlambatan',
        'total_pembayaran',
        'payment_status',
        'midtrans_order_id',
        'midtrans_response',
        'paid_at'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'jumlah' => 'integer'
    ];

    /**
     * Status yang tersedia
     */
    const STATUS_PENDING = 'pending';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_DIPINJAM = 'dipinjam';
    const STATUS_DIKEMBALIKAN = 'dikembalikan';

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';

    /**
     * Relasi dengan User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi dengan Barang
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Accessor untuk durasi peminjaman
     */
    public function getDurasiAttribute(): int
    {
        return $this->tanggal_pinjam->diffInDays($this->tanggal_kembali_rencana);
    }

    /**
     * Accessor untuk status terlambat
     */
    public function getTerlambatAttribute(): bool
    {
        if ($this->status !== self::STATUS_DIPINJAM) {
            return false;
        }
        
        return Carbon::now()->gt($this->tanggal_kembali_rencana);
    }

    /**
     * Accessor untuk hari terlambat
     */
    public function getHariTerlambatAttribute(): int
    {
        if (!$this->terlambat) {
            return 0;
        }
        
        return Carbon::now()->diffInDays($this->tanggal_kembali_rencana);
    }

    /**
     * Accessor untuk warna status
     */
    public function getWarnaStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_DISETUJUI => 'blue',
            self::STATUS_DITOLAK => 'red',
            self::STATUS_DIPINJAM => $this->terlambat ? 'red' : 'green',
            self::STATUS_DIKEMBALIKAN => 'gray',
            default => 'gray'
        };
    }

    /**
     * Accessor untuk label status
     */
    public function getLabelStatusAttribute(): string
    {
        $labels = [
            self::STATUS_PENDING => 'Menunggu Persetujuan',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_DIPINJAM => 'Sedang Dipinjam',
            self::STATUS_DIKEMBALIKAN => 'Dikembalikan'
        ];

        $label = $labels[$this->status] ?? 'Unknown';
        
        if ($this->status === self::STATUS_DIPINJAM && $this->terlambat) {
            $label .= ' (Terlambat ' . $this->hari_terlambat . ' hari)';
        }
        
        return $label;
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeByStatus(Builder $query, $status): void
    {
        if ($status) {
            $query->where('status', $status);
        }
    }

    /**
     * Scope untuk user tertentu
     */
    public function scopeByUser(Builder $query, $userId): void
    {
        if ($userId) {
            $query->where('user_id', $userId);
        }
    }

    /**
     * Scope untuk barang tertentu
     */
    public function scopeByBarang(Builder $query, $barangId): void
    {
        if ($barangId) {
            $query->where('barang_id', $barangId);
        }
    }

    /**
     * Scope untuk rentang tanggal
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): void
    {
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
        }
    }

    /**
     * Scope untuk peminjaman aktif
     */
    public function scopeAktif(Builder $query): void
    {
        $query->whereIn('status', [self::STATUS_DISETUJUI, self::STATUS_DIPINJAM]);
    }

    /**
     * Scope untuk peminjaman terlambat
     */
    public function scopeTerlambat(Builder $query): void
    {
        $query->where('status', self::STATUS_DIPINJAM)
              ->where('tanggal_kembali_rencana', '<', Carbon::now());
    }

    /**
     * Generate kode peminjaman otomatis
     */
    public static function generateKodePeminjaman(): string
    {
        $tanggal = Carbon::now()->format('Ymd');
        $count = self::whereDate('created_at', Carbon::today())->count() + 1;
        
        return 'PJM-' . $tanggal . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Update biaya peminjaman berdasarkan harga barang dan durasi
     */
    public function updateBiaya()
    {
        if (!$this->barang) {
            return;
        }

        // Hitung durasi dalam hari
        $durasi = $this->tanggal_pinjam->diffInDays($this->tanggal_kembali_rencana) + 1;
        
        // Hitung biaya sewa (durasi x harga per hari x jumlah)
        $this->total_biaya_sewa = $durasi * $this->barang->harga_sewa_per_hari * $this->jumlah;
        
        // Hitung deposit (deposit per unit x jumlah)
        $this->total_deposit = $this->barang->biaya_deposit * $this->jumlah;
        
        // Total pembayaran = biaya sewa + deposit
        $this->total_pembayaran = $this->total_biaya_sewa + $this->total_deposit;
        
        $this->save();
    }

    /**
     * Accessor untuk format total biaya sewa
     */
    public function getFormattedTotalBiayaSewaAttribute(): string
    {
        return 'Rp ' . number_format($this->total_biaya_sewa ?? 0, 0, ',', '.');
    }

    /**
     * Accessor untuk format total deposit
     */
    public function getFormattedTotalDepositAttribute(): string
    {
        return 'Rp ' . number_format($this->total_deposit ?? 0, 0, ',', '.');
    }

    /**
     * Accessor untuk format total pembayaran
     */
    public function getFormattedTotalPembayaranAttribute(): string
    {
        return 'Rp ' . number_format($this->total_pembayaran ?? 0, 0, ',', '.');
    }

    /**
     * Boot method untuk auto generate kode
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($peminjaman) {
            if (empty($peminjaman->kode_peminjaman)) {
                $peminjaman->kode_peminjaman = self::generateKodePeminjaman();
            }
        });
    }
}