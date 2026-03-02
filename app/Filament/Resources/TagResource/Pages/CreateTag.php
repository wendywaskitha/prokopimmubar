<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class CreateTag extends CreateRecord
{
    // use HasPageShield;
    
    protected static string $resource = TagResource::class;
}
