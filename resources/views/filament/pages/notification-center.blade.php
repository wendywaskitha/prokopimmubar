<x-filament-panels::page>
    <div class="space-y-4">
        @forelse ($this->notifications as $notification)
            <div class="p-4 rounded-lg border @if($notification->read_at) bg-white @else bg-blue-50 border-blue-200 @endif">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Dari: {{ $notification->data['author_name'] ?? 'System' }} ({{ $notification->data['author_role'] ?? 'User' }})
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Judul Berita: {{ $notification->data['news_title'] ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="text-xs text-gray-500 whitespace-nowrap">
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>
                @if(isset($notification->data['news_id']))
                    <div class="mt-3">
                        <x-filament::button 
                            href="{{ route('filament.admin.resources.news.edit', $notification->data['news_id']) }}" 
                            tag="a" 
                            color="primary" 
                            size="sm"
                        >
                            Lihat Berita
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12">
                <x-heroicon-o-bell class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
                <p class="mt-1 text-sm text-gray-500">Anda tidak memiliki notifikasi baru saat ini.</p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>