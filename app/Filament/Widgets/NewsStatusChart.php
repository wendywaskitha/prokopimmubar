<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class NewsStatusChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'newsStatusChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Distribusi Status Berita';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Hitung jumlah berita berdasarkan status
        $publishedCount = News::where('status', 'published')->count();
        $draftCount = News::where('status', 'draft')->count();
        $archivedCount = News::where('status', 'archived')->count();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => [$publishedCount, $draftCount, $archivedCount],
            'labels' => ['Dipublikasikan', 'Draft', 'Diarsipkan'],
            'legend' => [
                'show' => true,
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'theme' => [
                'palette' => 'palette4',
            ],
        ];
    }

    /**
     * Widget footer description
     */
    protected static ?string $description = 'Statistik distribusi berita berdasarkan status';
}