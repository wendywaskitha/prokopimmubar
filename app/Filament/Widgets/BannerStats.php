<?php

namespace App\Filament\Widgets;

use App\Models\Banner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BannerStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Banner', Banner::count())
                ->description('Semua banner')
                ->descriptionIcon('heroicon-m-flag')
                ->color('primary'),
            Stat::make('Banner Aktif', Banner::where('is_active', true)->count())
                ->description('Banner yang sedang ditampilkan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Total Klik', Banner::sum('click_count'))
                ->description('Jumlah klik pada semua banner')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('info'),
        ];
    }
}