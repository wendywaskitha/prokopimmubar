<?php

namespace App\Filament\Resources\SocialMediaResource\Pages;

use App\Filament\Resources\SocialMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class CreateSocialMedia extends CreateRecord
{
    // use HasPageShield;

    protected static string $resource = SocialMediaResource::class;
}
