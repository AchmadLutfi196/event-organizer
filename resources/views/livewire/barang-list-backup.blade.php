<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Barang</h1>
        <p class="mt-2 text-gray-600">Temukan barang yang Anda butuhkan</p>
    </div>

    <!-- New Items Alert -->
    @if($showNewItemsAlert && $newItemsCount > 0)
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4" wire:transition>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        {{ $newItemsCount }} barang baru tersedia!
                    </h3>
                    <p class="mt-1 text-sm text-blue-700">
                        Klik tombol di bawah untuk melihat barang terbaru.
                    </p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button wire:click="showNewItems" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                    Tampilkan Sekarang
                </button>
                <button wire:click="dismissNewItemsAlert"
                        class="text-blue-500 hover:text-blue-700 p-2">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter & Search -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <div class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700">Pencarian</label>
                    <input type="text" wire:model.live.debounce.300ms="search" id="search" 
                           placeholder="Cari nama barang, kode, atau lokasi..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Kategori -->
                <div class="md:w-48">
                    <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select wire:model.live="kategori" id="kategori" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kondisi -->
                <div class="md:w-48">
                    <label for="kondisi" class="block text-sm font-medium text-gray-700">Kondisi</label>
                    <select wire:model.live="kondisi" id="kondisi" 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kondisi</option>
                        <option value="baik">Baik</option>
                        <option value="rusak ringan">Rusak Ringan</option>
                        <option value="perlu perbaikan">Perlu Perbaikan</option>
                    </select>
                </div>

                <!-- Button -->
                <div class="flex space-x-2">
                    <button wire:click="resetFilters" 
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:ring-2 focus:ring-gray-500">
                        Reset
                    </button>
                    <button wire:click="refresh" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="mb-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center">
            <svg class="animate-spin h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-blue-700">Memuat data...</span>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-4">
        <p class="text-sm text-gray-600">
            Menampilkan {{ $barangs->firstItem() ?? 0 }} - {{ $barangs->lastItem() ?? 0 }} dari {{ $barangs->total() }} barang
        </p>
    </div>

    <!-- Barang Grid -->
    @if($barangs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" wire:loading.class="opacity-50">
            @foreach($barangs as $barang)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow 
                            {{ in_array($barang->id, $newBarangIds) ? 'ring-2 ring-blue-500 ring-opacity-50 animate-pulse' : '' }}"
                     wire:key="barang-{{ $barang->id }}">
                    
                    <!-- New Item Badge -->
                    @if(in_array($barang->id, $newBarangIds))
                    <div class="relative">
                        <div class="absolute top-2 left-2 z-10">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 animate-bounce">
                                Baru!
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- Image -->
                    <div class="h-48 bg-gray-200 relative">
                        @if($barang->foto)
                            <img src="{{ Storage::url($barang->foto) }}" alt="{{ $barang->nama }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $barang->stok_tersedia > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $barang->stok_tersedia > 0 ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Category -->
                        <div class="mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $barang->kategori->nama }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $barang->nama }}</h3>
                        
                        <!-- Price Info -->
                        @if($barang->harga_sewa_per_hari > 0 || $barang->biaya_deposit > 0)
                        <div class="mb-3">
                            @if($barang->harga_sewa_per_hari > 0)
                            <div class="text-lg font-bold text-green-600">{{ $barang->formatted_harga_sewa }}/hari</div>
                            @endif
                            @if($barang->biaya_deposit > 0)
                            <div class="text-sm text-blue-600">Deposit: {{ $barang->formatted_deposit }}</div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Code -->
                        <p class="text-sm text-gray-500 mb-2">{{ $barang->kode_barang }}</p>

                        <!-- Stock Info -->
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm text-gray-600">Stok: {{ $barang->stok_tersedia }}/{{ $barang->stok }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $barang->kondisi == 'baik' ? 'bg-green-100 text-green-800' : 
                                   ($barang->kondisi == 'rusak ringan' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($barang->kondisi) }}
                            </span>
                        </div>

                        <!-- Location -->
                        <p class="text-sm text-gray-600 mb-3">📍 {{ $barang->lokasi }}</p>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('frontend.barang.show', $barang) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-3 rounded-md hover:bg-blue-700 text-sm">
                                Lihat Detail
                            </a>
                            @auth
                                @if($barang->stok_tersedia > 0)
                                    <a href="{{ route('frontend.peminjaman.create', ['barang_id' => $barang->id]) }}" 
                                       class="bg-green-600 text-white py-2 px-3 rounded-md hover:bg-green-700 text-sm">
                                        Pinjam
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $barangs->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12" wire:loading.class="hidden">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada barang ditemukan</h3>
            <p class="mt-1 text-sm text-gray-500">Coba ubah filter pencarian Anda.</p>
        </div>
    @endif
</div>

@script
<script>
    // Handle real-time notifications
    $wire.on('barang-created', (event) => {
        const data = event[0];
        showNotification('success', 'Barang Baru!', data.message);
    });

    $wire.on('barang-updated', (event) => {
        const data = event[0];
        showNotification('info', 'Barang Diperbarui', data.message);
    });

    $wire.on('barang-deleted', (event) => {
        const data = event[0];
        showNotification('warning', 'Barang Dihapus', data.message);
    });

    // Auto-refresh countdown for new items
    $wire.on('auto-refresh-countdown', () => {
        let countdown = 5;
        const interval = setInterval(() => {
            countdown--;
            if (countdown === 0) {
                clearInterval(interval);
                $wire.call('showNewItems');
            }
        }, 1000);
    });

    // Update URL without page reload
    $wire.on('url-updated', (params) => {
        const url = new URL(window.location);
        Object.keys(params[0]).forEach(key => {
            if (params[0][key]) {
                url.searchParams.set(key, params[0][key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.replaceState({}, '', url);
    });

    function showNotification(type, title, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden z-50 transform translate-x-full`;
        
        let bgColor = 'bg-blue-50';
        let iconColor = 'text-blue-400';
        let textColor = 'text-blue-800';
        
        if (type === 'success') {
            bgColor = 'bg-green-50';
            iconColor = 'text-green-400';
            textColor = 'text-green-800';
        } else if (type === 'warning') {
            bgColor = 'bg-yellow-50';
            iconColor = 'text-yellow-400';
            textColor = 'text-yellow-800';
        } else if (type === 'error') {
            bgColor = 'bg-red-50';
            iconColor = 'text-red-400';
            textColor = 'text-red-800';
        }
        
        notification.innerHTML = `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">${title}</p>
                        <p class="mt-1 text-sm text-gray-500">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }
</script>
@endscript
