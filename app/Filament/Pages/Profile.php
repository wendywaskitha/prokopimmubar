<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Profile extends Page
{
    // use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $navigationLabel = 'Profil Pengguna';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('👤 Informasi Personal')
                    ->description('Perbarui informasi dasar profil Anda untuk pengalaman yang lebih personal.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Masukkan nama lengkap Anda')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user')
                            ->helperText('Nama ini akan ditampilkan di seluruh sistem')
                            ->live(onBlur: true),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('contoh@email.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-at-symbol')
                            ->helperText('Email untuk login dan notifikasi sistem')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->placeholder('08xxxxxxxxxx')
                            ->tel()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-o-phone')
                            ->helperText('Nomor telepon aktif untuk keperluan komunikasi')
                            ->mask('9999-9999-9999'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('🖼️ Foto & Identitas Visual')
                    ->description('Upload foto profil dan atur identitas visual Anda.')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('📸 Foto Profil')
                            ->image()
                            ->directory('avatars')
                            ->avatar()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024) // 1MB
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->helperText('Upload foto profil persegi. Format: JPG, PNG, WebP. Maksimal 1MB.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('💼 Informasi Profesional')
                    ->description('Tambahkan informasi profesional dan biografi singkat.')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        TextInput::make('position')
                            ->label('Jabatan/Posisi')
                            ->placeholder('Contoh: Manager IT, Staff Admin, Developer')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-briefcase')
                            ->helperText('Jabatan atau posisi Anda dalam organisasi'),

                        Textarea::make('bio')
                            ->label('Biografi Singkat')
                            ->placeholder('Ceritakan sedikit tentang diri Anda, pengalaman, atau keahlian...')
                            ->rows(4)
                            ->maxLength(500)
                            ->helperText('Deskripsi singkat tentang Anda (maksimal 500 karakter)')
                            ->columnSpanFull()
                            ->live(onBlur: true),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();
        $user->update($data);

        Notification::make()
            ->title('Profil berhasil diperbarui.')
            ->success()
            ->send();

        // Refresh form dengan data terbaru
        $this->form->fill($user->fresh()->toArray());
    }

    public function getTitle(): string
    {
        return 'Profil Pengguna';
    }

    public function getHeading(): string
    {
        return '👋 Halo, ' . Auth::user()->name;
    }

    public function getSubheading(): ?string
    {
        return 'Kelola informasi profil Anda dengan mudah dan aman';
    }
}
