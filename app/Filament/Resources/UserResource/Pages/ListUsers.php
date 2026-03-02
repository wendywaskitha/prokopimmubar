<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Widgets\UserStatsWidget;
use App\Filament\Widgets\UsersByRoleChart;
use App\Filament\Widgets\UserRegistrationTrendChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use STS\FilamentImpersonate\Tables\Actions\ImpersonateAction;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ListUsers extends ListRecords
{
    // use HasPageShield;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class,
            UserRegistrationTrendChart::class,
            UsersByRoleChart::class,
        ];
    }

    // protected function getTableActions(): array
    // {
    //     return [
    //         ImpersonateAction::make(),
    //     ];
    // }
}
