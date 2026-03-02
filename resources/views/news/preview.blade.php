<!DOCTYPE html>
<html>
<head>
    <title>Preview Berita</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto p-6 bg-white mt-8 rounded-lg shadow">
        <h1 class="text-3xl font-bold mb-4">{{ $news->title }}</h1>
        
        <div class="flex items-center text-gray-600 mb-6">
            <span>Dipublikasikan pada {{ $news->published_at ? $news->published_at->format('d M Y H:i') : 'Tanggal tidak tersedia' }}</span>
        </div>
        
        @if($news->featured_image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $news->featured_image) }}" alt="{{ $news->title }}" class="w-full rounded-lg">
            </div>
        @endif
        
        <div class="prose max-w-none">
            {!! Str::markdown($news->content) !!}
        </div>
        
        @if($news->excerpt)
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Ringkasan:</h3>
                <p>{{ $news->excerpt }}</p>
            </div>
        @endif
    </div>
</body>
</html>