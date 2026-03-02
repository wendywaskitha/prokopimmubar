<div>
    <!-- Embedded Styles -->
    <style>
        .fi-section-content-ctn {
            @apply bg-white/80 backdrop-blur-sm;
        }

        .fi-section-header {
            @apply bg-gradient-to-r from-gray-50 to-blue-50/50;
        }

        .fi-input {
            @apply transition-all duration-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400;
        }

        .fi-btn-primary {
            @apply bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200;
        }

        /* Dark mode overrides */
        @media (prefers-color-scheme: dark) {
            .fi-section-content-ctn {
                background-color: rgba(255, 255, 255, 0.1);
            }

            .fi-section-header {
                background: linear-gradient(to right, #374151, #1e3a8a);
            }
        }
    </style>

    <!-- Main Content Wrapper -->
    <div class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-6 mb-6">
            {{-- <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-sm font-medium text-blue-100">Dashboard</span>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-sm font-medium text-white" aria-current="page">Profil</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div> --}}
        </div>

        <!-- Main Content -->
        <div class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                        <!-- Page Header Info -->
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <h1 class="text-2xl font-bold text-gray-900">Profil Pengguna</h1>
                            <p class="text-gray-500 mt-1">Kelola informasi profil dan preferensi akun Anda</p>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-gray-200">
                            <div class="flex-shrink-0">
                                @if(Auth::user()->avatar)
                                    <img class="h-16 w-16 rounded-full border-4 border-white/20"
                                         src="{{ Storage::url(Auth::user()->avatar) }}"
                                         alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-4 border-white">
                                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-lg font-bold truncate text-gray-900">{{ Auth::user()->name }}</h2>
                                <p class="text-sm text-gray-500">
                                    @if(Auth::user()->position)
                                        {{ Auth::user()->position }}
                                    @else
                                        Anggota
                                    @endif
                                </p>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tips Profil
                        </h3>
                        <div class="space-y-4 text-sm text-gray-600">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-2 h-2 bg-green-400 rounded-full mt-2"></div>
                                <p>Lengkapi foto profil untuk tampilan yang lebih personal</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                                <p>Tambahkan biografi singkat untuk memperkenalkan diri</p>
                            </div>
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-2 h-2 bg-purple-400 rounded-full mt-2"></div>
                                <p>Perbarui informasi kontak secara berkala</p>
                            </div>
                        </div>

                        <!-- User Stats -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-3">Statistik Akun</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Status</span>
                                    <span class="text-green-600 font-medium">Aktif</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Terakhir login</span>
                                    <span class="text-gray-900">{{ Auth::user()->updated_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Bergabung sejak</span>
                                    <span class="text-gray-900">{{ Auth::user()->created_at->format('F Y') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Role</span>
                                    <span class="text-gray-900 capitalize">
                                        @if(Auth::user()->hasRole('super_admin'))
                                            Super Admin
                                        @elseif(Auth::user()->hasRole('editor'))
                                            Editor
                                        @elseif(Auth::user()->hasRole('penulis'))
                                            Penulis
                                        @elseif(Auth::user()->hasRole('kontributor'))
                                            Kontributor
                                        @else
                                            User
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Form Header -->
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                        <svg class="h-6 w-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Informasi Profil
                                    </h2>
                                    <p class="text-gray-500 text-sm mt-1">Perbarui informasi profil Anda dengan data yang akurat</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="h-2 w-2 bg-green-400 rounded-full animate-pulse"></div>
                                    <span class="text-sm text-gray-500">Auto-save</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Content -->
                        <form wire:submit.prevent="save" class="p-6">
                            {{ $this->form }}

                            <!-- Action Buttons -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Data Anda aman dan terenkripsi
                                    </div>
                                    <div class="flex space-x-3">
                                        <button type="button"
                                                onclick="window.location.reload()"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Reset
                                        </button>
                                        <x-filament::button type="submit"
                                                           class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Simpan Perubahan
                                        </x-filament::button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Additional Info Card -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informasi Keamanan</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Email Anda tidak dapat diubah dari halaman ini untuk alasan keamanan. Hubungi administrator jika diperlukan perubahan email.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
