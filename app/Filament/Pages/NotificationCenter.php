<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class NotificationCenter extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static string $view = 'filament.pages.notification-center';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 4;

    public function mount(): void
    {
        // Mark all notifications as read when user visits the notification center
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function getNotificationsProperty()
    {
        return Auth::user()->notifications()->latest()->limit(50)->get();
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();

        // Only show badge for super_admin and editor roles
        if ($user && ($user->isSuperAdmin() || $user->isEditor())) {
            $unreadCount = $user->unreadNotifications()->count();
            return $unreadCount > 0 ? number_format($unreadCount) : null;
        }

        return null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public function getTitle(): string
    {
        return 'Pusat Notifikasi';
    }

    public function getHeading(): string
    {
        return '🔔 Pusat Notifikasi';
    }

    public function getSubheading(): ?string
    {
        return 'Lihat semua notifikasi sistem';
    }
}
