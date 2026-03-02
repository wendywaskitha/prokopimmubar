<?php

namespace App\Filament\Widgets;

use App\Models\Complaint;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ComplaintStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Aduan', Complaint::count())
                ->description('Jumlah seluruh aduan yang diterima')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
            Stat::make('Aduan Baru', Complaint::where('status', 'baru')->count())
                ->description('Aduan yang belum diproses')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
            Stat::make('Diproses', Complaint::where('status', 'diproses')->count())
                ->description('Aduan yang sedang dalam penanganan')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
            Stat::make('Selesai', Complaint::where('status', 'selesai')->count())
                ->description('Aduan yang telah diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}