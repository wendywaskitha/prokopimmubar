<?php

namespace App\Filament\Resources\WorkProgramResource\Pages;

use App\Filament\Resources\WorkProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class EditWorkProgram extends EditRecord
{
    // use HasPageShield;

    protected static string $resource = WorkProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
