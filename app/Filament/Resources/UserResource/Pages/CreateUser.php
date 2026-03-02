<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class CreateUser extends CreateRecord
{
    // use HasPageShield;

    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan role yang dipilih untuk ditetapkan setelah user dibuat
        $this->roleToAssign = $data['role'] ?? null;
        unset($data['role']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Tetapkan role yang dipilih setelah user dibuat
        if (isset($this->roleToAssign)) {
            $this->record->assignRole($this->roleToAssign);
        }
    }
}
