<div>
    <div class="flex flex-row gap-2 items-center">
        @php
            $logoPath = null;
            $title = config('app.name');
            $subtitle = config('app.company_name', '');
            
            try {
                if (class_exists(\App\Models\Setting::class) && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    $logoPath = \App\Models\Setting::get('app_logo');
                    $title = \App\Models\Setting::get('app_name', config('app.name'));
                    $subtitle = \App\Models\Setting::get('app_subtitle', config('app.company_name'));
                }
            } catch (\Exception $e) {
                // Silently fail if settings table is not accessible
            }
        @endphp

        @if($logoPath)
            <div class="w-5 h-5">
                <img src="{{ Storage::url($logoPath) }}" alt="{{ $title }}">
            </div>
        @endif

        <div class="flex flex-col pt-2">
            <div class="font-bold text-nowrap tracking-tight">{{ $title }}</div>
            <div class="text-xs font-medium text-nowrap">{{ $subtitle }}</div>
        </div>
    </div>
</div>
