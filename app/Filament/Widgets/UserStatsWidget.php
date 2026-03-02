<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengguna', User::count())
                ->description('Jumlah seluruh pengguna terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Admin', User::role('super_admin')->count())
                ->description('Pengguna dengan akses penuh')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),
            Stat::make('Editor', User::role('editor')->count())
                ->description('Pengelola konten dan media')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('info'),
            Stat::make('Penulis', User::role('penulis')->count())
                ->description('Pembuat konten')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
            Stat::make('Kontributor', User::role('kontributor')->count())
                ->description('Pengirim konten untuk review')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('gray'),
        ];
    }
}