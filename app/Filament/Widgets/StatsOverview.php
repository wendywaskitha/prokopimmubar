<?php

namespace App\Filament\Widgets;

use App\Models\Complaint;
use App\Models\Gallery;
use App\Models\News;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Berita', News::count())
                ->description('Jumlah berita yang telah dipublikasikan')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('success'),
            
            Card::make('Total Galeri', Gallery::count())
                ->description('Jumlah galeri foto yang tersedia')
                ->descriptionIcon('heroicon-o-photo')
                ->color('warning'),
            
            Card::make('Total Aduan', Complaint::count())
                ->description('Jumlah aduan masyarakat yang diterima')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color('primary'),
            
            Card::make('Total Pengguna', User::count())
                ->description('Jumlah pengguna sistem terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
        ];
    }
}