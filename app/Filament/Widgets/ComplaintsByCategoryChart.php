<?php

namespace App\Filament\Widgets;

use App\Models\Complaint;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ComplaintsByCategoryChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'complaintsByCategoryChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Aduan Berdasarkan Kategori';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Ambil data aduan berdasarkan kategori
        $categories = Complaint::select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->get();

        $categoryNames = $categories->pluck('category')->toArray();
        $categoryCounts = $categories->pluck('count')->toArray();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $categoryCounts,
            'labels' => $categoryNames,
            'legend' => [
                'show' => true,
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'theme' => [
                'palette' => 'palette6',
            ],
        ];
    }

    /**
     * Widget footer description
     */
    protected static ?string $description = 'Distribusi aduan berdasarkan kategori';
}