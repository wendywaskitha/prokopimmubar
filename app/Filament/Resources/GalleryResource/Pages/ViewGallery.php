<?php

namespace App\Filament\Resources\GalleryResource\Pages;

use App\Filament\Resources\GalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ViewGallery extends ViewRecord
{
    // use HasPageShield;

    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
