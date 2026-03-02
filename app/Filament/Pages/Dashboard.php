<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\NewsByCategoryChart;
use App\Filament\Widgets\NewsStatusChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Dashboard extends Page
{
    // use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -2;

    // protected static ?string $navigationGroup = 'Sistem';

    protected static string $view = 'filament.pages.dashboard';

    public function mount(): void
    {
        //
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getHeading(): string
    {
        return '📊 Dashboard Overview';
    }

    public function getSubheading(): ?string
    {
        return 'Ringkasan statistik dan aktivitas terkini';
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();
        
        // Default widgets
        $widgets = [
            StatsOverview::class,
        ];
        
        // Tambahkan chart widgets hanya untuk admin dan editor
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('editor'))) {
            $widgets[] = NewsStatusChart::class;
            $widgets[] = NewsByCategoryChart::class;
        }
        
        return $widgets;
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add any footer widgets here if needed
        ];
    }
}
