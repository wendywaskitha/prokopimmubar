<x-filament::page>
    {{-- <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Welcome Card -->
        <div class="col-span-1 md:col-span-2 lg:col-span-3">
            <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <x-heroicon-o-hand-raised class="w-6 h-6" />
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Selamat Datang, {{ auth()->user()->name }}!
                        </h2>
                        <p class="mt-1 text-gray-600">
                            Ini adalah dashboard admin portal berita Warta Daerah Kabupaten Muna Barat.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-span-1 md:col-span-2">
            <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🕒 Aktivitas Terkini</h3>
                <div class="space-y-4">
                    @php
                        $recentNews = \App\Models\News::latest()->take(5)->get();
                    @endphp

                    @if($recentNews->count() > 0)
                        @foreach($recentNews as $news)
                            <div class="flex items-start p-3 hover:bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <x-heroicon-o-newspaper class="w-5 h-5 text-blue-600" />
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $news->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        Dipublikasikan {{ $news->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-sm">Belum ada berita yang dipublikasikan.</p>
                    @endif
                </div>
            </div>
        </div> --}}

        <!-- User Information -->
        {{-- <div class="col-span-1">
            <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Informasi Akun</h3>
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
                        @else
                            <x-heroicon-o-user class="w-8 h-8 text-gray-500" />
                        @endif
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">{{ auth()->user()->name }}</h4>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    <div class="mt-4 w-full">
                        <a href="{{ route('filament.admin.pages.profile') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
    {{-- </div> --}}
</x-filament::page>
