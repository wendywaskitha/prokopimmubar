<?php

namespace App\Filament\Widgets;

use App\Models\Complaint;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ComplaintsTrendChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'complaintsTrendChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Tren Aduan per Bulan';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Ambil data aduan per bulan untuk 6 bulan terakhir
        $complaints = Complaint::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->selectRaw('count(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = $complaints->pluck('month')->map(function ($month) {
            return date('M Y', strtotime($month));
        })->toArray();
        
        $counts = $complaints->pluck('count')->toArray();

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Aduan',
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
                    'text' => 'Jumlah Aduan',
                ],
            ],
            'colors' => ['#3b82f6'],
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
    protected static ?string $description = 'Tren aduan masyarakat selama 6 bulan terakhir';
}