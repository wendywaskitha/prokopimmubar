<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UsersByRoleChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'usersByRoleChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Distribusi Pengguna Berdasarkan Role';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        // Ambil data pengguna berdasarkan role
        $superAdminCount = User::role('super_admin')->count();
        $editorCount = User::role('editor')->count();
        $penulisCount = User::role('penulis')->count();
        $kontributorCount = User::role('kontributor')->count();

        $roleNames = ['Super Admin', 'Editor', 'Penulis', 'Kontributor'];
        $roleCounts = [$superAdminCount, $editorCount, $penulisCount, $kontributorCount];

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $roleCounts,
            'labels' => $roleNames,
            'legend' => [
                'show' => true,
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'theme' => [
                'palette' => 'palette5',
            ],
        ];
    }

    /**
     * Widget footer description
     */
    protected static ?string $description = 'Proporsi pengguna berdasarkan role mereka';
}