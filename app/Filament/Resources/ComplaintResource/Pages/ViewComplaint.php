<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ViewComplaint extends ViewRecord
{
    // use HasPageShield;

    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
