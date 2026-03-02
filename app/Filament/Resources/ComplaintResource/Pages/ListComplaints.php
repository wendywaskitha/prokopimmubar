<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Filament\Widgets\ComplaintStatsWidget;
use App\Filament\Widgets\ComplaintsByCategoryChart;
use App\Filament\Widgets\ComplaintsTrendChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ListComplaints extends ListRecords
{
    // use HasPageShield;

    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ComplaintStatsWidget::class,
            ComplaintsTrendChart::class,
            ComplaintsByCategoryChart::class,
        ];
    }
}
