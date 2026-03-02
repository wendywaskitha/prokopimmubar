<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class EditUser extends EditRecord
{
    // use HasPageShield;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Simpan role yang dipilih untuk ditetapkan setelah user disimpan
        $this->roleToAssign = $data['role'] ?? null;
        unset($data['role']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Tetapkan role yang dipilih setelah user disimpan
        if (isset($this->roleToAssign)) {
            $this->record->syncRoles([$this->roleToAssign]);
        }
    }
}
