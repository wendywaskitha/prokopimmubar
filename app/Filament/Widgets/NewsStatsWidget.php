<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Berita', News::count())
                ->description('Jumlah seluruh berita')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary'),
            Stat::make('Berita Draft', News::where('status', 'draft')->count())
                ->description('Berita yang belum dipublikasikan')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),
            Stat::make('Berita Diarsipkan', News::where('status', 'archived')->count())
                ->description('Berita yang sudah diarsipkan')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('secondary'),
            Stat::make('Berita Dipublikasikan', News::where('status', 'published')->count())
                ->description('Berita yang sedang aktif')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}