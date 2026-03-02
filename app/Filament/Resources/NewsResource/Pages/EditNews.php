<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Facades\Auth;

class EditNews extends EditRecord
{
    // use HasPageShield;

    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $actions = [];

        // Hanya tampilkan delete action untuk user yang berhak
        if ($user->can('delete_news') || $user->hasCustomRole('editor') ||
            ($user->hasCustomRole('penulis') && $this->record->author_id === $user->id)) {
            $actions[] = Actions\DeleteAction::make();
        }

        return $actions;
    }

    protected function authorizeAccess(): void
    {
        $user = Auth::user();
        $news = $this->getRecord();

        // Izinkan akses jika user adalah super admin
        if ($user->hasCustomRole('super_admin')) {
            return;
        }

        // Izinkan akses jika user adalah editor
        if ($user->hasCustomRole('editor')) {
            return;
        }

        // Izinkan akses jika user adalah penulis dan berita milik mereka sendiri
        if ($user->hasCustomRole('penulis') && $news->author_id === $user->id) {
            return;
        }

        // Larang akses jika tidak memenuhi syarat
        abort(403);
    }
}
