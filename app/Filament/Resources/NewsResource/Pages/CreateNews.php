<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Facades\Auth;
use App\Models\News;
use App\Models\User;
use App\Notifications\NewsSubmittedNotification;

class CreateNews extends CreateRecord
{
    // use HasPageShield;

    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // Set author_id ke user yang sedang login
        $data['author_id'] = $user->id;

        // Jika user adalah kontributor, set status ke draft
        if ($user->hasCustomRole('kontributor')) {
            $data['status'] = 'draft';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = Auth::user();
        
        // Only send notifications if the user is penulis or kontributor
        if ($user->isPenulis() || $user->isKontributor()) {
            // Get the newly created news article
            $news = $this->record;
            
            // Get all super_admin and editor users
            $recipients = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['super_admin', 'editor']);
            })->get();
            
            // Send notification to each recipient
            foreach ($recipients as $recipient) {
                $recipient->notify(new NewsSubmittedNotification($news));
            }
        }
    }
}
