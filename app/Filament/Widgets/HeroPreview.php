<?php

namespace App\Filament\Widgets;

use App\Models\Hero;
use Filament\Widgets\Widget;

class HeroPreview extends Widget
{
    protected static string $view = 'filament.widgets.hero-preview';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'heroes' => Hero::where('is_active', true)->orderBy('sort_order')->get(),
        ];
    }
}