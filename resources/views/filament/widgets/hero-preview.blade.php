<div class="p-6 bg-white rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Preview Hero Carousel</h3>
    
    @if($heroes->isEmpty())
        <p class="text-gray-500">Belum ada hero carousel yang aktif.</p>
    @else
        <div class="relative overflow-hidden rounded-lg">
            <div class="flex transition-transform duration-500 ease-in-out" style="transform: translateX(0);">
                @foreach($heroes as $hero)
                    <div class="w-full flex-shrink-0">
                        <div class="relative h-64 bg-gray-200 rounded-lg overflow-hidden">
                            @if($hero->image)
                                <img src="{{ asset('storage/' . $hero->image) }}" alt="{{ $hero->title }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="text-xl font-bold">{{ $hero->title }}</h4>
                                @if($hero->description)
                                    <p class="mt-1">{{ $hero->description }}</p>
                                @endif
                                @if($hero->link)
                                    <a href="{{ $hero->link }}" class="mt-2 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                                        Selengkapnya
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($heroes->count() > 1)
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    @foreach($heroes as $index => $hero)
                        <button class="w-3 h-3 rounded-full bg-white/50 hover:bg-white"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>