<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UserRegistrationTrendChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'userRegistrationTrendChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Tren Pendaftaran Pengguna';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Ambil data pendaftaran pengguna per bulan untuk 6 bulan terakhir
        $users = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->selectRaw('count(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = $users->pluck('month')->map(function ($month) {
            return date('M Y', strtotime($month));
        })->toArray();
        
        $counts = $users->pluck('count')->toArray();

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Pengguna Baru',
                    'data' => $counts,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Pengguna',
                ],
            ],
            'colors' => ['#8b5cf6'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'opacityFrom' => 0.6,
                    'opacityTo' => 0.1,
                ],
            ],
        ];
    }

    /**
     * Widget footer description
     */
    protected static ?string $description = 'Tren pendaftaran pengguna selama 6 bulan terakhir';
}