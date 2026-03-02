<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\News;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class NewsByCategoryChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'newsByCategoryChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Berita Berdasarkan Kategori';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Ambil 10 kategori teratas dengan jumlah berita terbanyak
        $categories = Category::withCount('news')
            ->orderBy('news_count', 'desc')
            ->limit(10)
            ->get();

        $categoryNames = $categories->pluck('name')->toArray();
        $newsCounts = $categories->pluck('news_count')->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Berita',
                    'data' => $newsCounts,
                ],
            ],
            'xaxis' => [
                'categories' => $categoryNames,
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Berita',
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal' => false,
                ],
            ],
        ];
    }

    /**
     * Widget footer description
     */
    protected static ?string $description = '10 kategori dengan jumlah berita terbanyak';
}