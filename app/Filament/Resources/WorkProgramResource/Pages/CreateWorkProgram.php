<?php

namespace App\Filament\Resources\WorkProgramResource\Pages;

use App\Filament\Resources\WorkProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class CreateWorkProgram extends CreateRecord
{
    // use HasPageShield;

    protected static string $resource = WorkProgramResource::class;
}
