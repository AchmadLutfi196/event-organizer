<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;

class RealtimeDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static string $view = 'filament.pages.realtime-dashboard';
    
    protected static ?string $navigationLabel = 'Real-time Dashboard';
    
    protected static ?string $title = 'Real-time Dashboard';
    
    protected static ?int $navigationSort = 1;

    public array $realtimeStats = [];
    public bool $isConnected = false;
    public array $recentActivity = [];

    public function mount(): void
    {
        $this->loadRealtimeStats();
        $this->loadRecentActivity();
    }

    #[On('echo:barang-updates,barang.updated')]
    #[On('echo:peminjaman-updates,peminjaman.updated')]
    #[On('echo:user-updates,user.updated')]
    #[On('echo:kategori-updates,kategori.updated')]
    public function handleRealtimeUpdate($event): void
    {
        $this->loadRealtimeStats();
        $this->addToRecentActivity($event);
        $this->dispatch('stats-updated');
    }

    #[On('echo-private:admin-notifications,admin.notification')]
    public function handleAdminNotification($event): void
    {
        $this->addToRecentActivity([
            'type' => 'notification',
            'title' => $event['title'],
            'message' => $event['message'],
            'timestamp' => now()
        ]);
    }

    public function loadRealtimeStats(): void
    {
        $this->realtimeStats = [
            'total_barang' => \App\Models\Barang::count(),
            'barang_stok_rendah' => \App\Models\Barang::where('stok', '<=', 5)->count(),
            'peminjaman_pending' => \App\Models\Peminjaman::where('status', 'pending')->count(),
            'peminjaman_aktif' => \App\Models\Peminjaman::where('status', 'dipinjam')->count(),
            'total_users' => \App\Models\User::where('role', 'user')->count(),
            'peminjaman_hari_ini' => \App\Models\Peminjaman::whereDate('created_at', today())->count(),
            'last_updated' => now()->format('H:i:s')
        ];
    }

    public function loadRecentActivity(): void
    {
        // Load recent activity from various sources
        $this->recentActivity = collect([
            // Recent peminjaman
            ...\App\Models\Peminjaman::latest()
                ->take(5)
                ->get()
                ->map(fn($p) => [
                    'type' => 'peminjaman',
                    'title' => 'Peminjaman Baru',
                    'message' => "Peminjaman {$p->kode_peminjaman} oleh {$p->user?->name}",
                    'timestamp' => $p->created_at,
                    'data' => ['id' => $p->id, 'status' => $p->status]
                ]),
            
            // Recent barang updates
            ...\App\Models\Barang::latest('updated_at')
                ->take(3)
                ->get()
                ->map(fn($b) => [
                    'type' => 'barang',
                    'title' => 'Barang Diperbarui',
                    'message' => "Barang {$b->nama} telah diperbarui",
                    'timestamp' => $b->updated_at,
                    'data' => ['id' => $b->id, 'stok' => $b->stok]
                ])
        ])
        ->sortByDesc('timestamp')
        ->take(10)
        ->values()
        ->toArray();
    }

    public function addToRecentActivity(array $event): void
    {
        array_unshift($this->recentActivity, [
            'type' => $event['type'] ?? 'update',
            'title' => $event['title'] ?? 'Update',
            'message' => $event['message'] ?? 'Data telah diperbarui',
            'timestamp' => now(),
            'data' => $event['data'] ?? []
        ]);

        // Keep only last 10 activities
        $this->recentActivity = array_slice($this->recentActivity, 0, 10);
    }

    public function refreshData(): void
    {
        $this->loadRealtimeStats();
        $this->loadRecentActivity();
        $this->dispatch('data-refreshed');
    }

    public function testConnection(): void
    {
        $this->isConnected = !$this->isConnected;
        $this->dispatch('connection-tested', ['connected' => $this->isConnected]);
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->realtimeStats,
            'activity' => $this->recentActivity,
            'connected' => $this->isConnected
        ];
    }
}
