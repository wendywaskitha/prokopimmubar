<?php

namespace App\Filament\Pages;

use App\Repositories\SettingsRepository;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationGroup = 'Pengaturan Website';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getSettingsData());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])
                ->schema([
                    Grid::make([
                        'default' => 1,
                    ])
                    ->columnSpan([
                        'md' => 2,
                    ])
                    ->schema([
                        Section::make('🏢 Informasi Aplikasi')
                            ->description('Atur informasi dasar aplikasi yang akan ditampilkan di seluruh sistem.')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                TextInput::make('app_name')
                                    ->label('Nama Aplikasi')
                                    ->placeholder('Masukkan nama aplikasi')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-clipboard-document')
                                    ->helperText('Nama aplikasi akan ditampilkan di title bar dan header'),

                                TextInput::make('app_subtitle')
                                    ->label('Subtitle Aplikasi')
                                    ->placeholder('Masukkan subtitle aplikasi')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-tag')
                                    ->helperText('Subtitle aplikasi akan ditampilkan di bawah nama aplikasi'),

                                Textarea::make('app_description')
                                    ->label('Deskripsi Aplikasi')
                                    ->placeholder('Deskripsikan aplikasi ini...')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->helperText('Deskripsi singkat tentang aplikasi (maksimal 500 karakter)')
                                    ->columnSpanFull(),

                                TextInput::make('app_address')
                                    ->label('Alamat')
                                    ->placeholder('Masukkan alamat lengkap')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-map-pin')
                                    ->helperText('Alamat kantor atau organisasi'),

                                TextInput::make('app_email')
                                    ->label('Email')
                                    ->placeholder('admin@domain.com')
                                    ->email()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->helperText('Email kontak untuk pengunjung'),

                                TextInput::make('app_phone')
                                    ->label('Telepon')
                                    ->placeholder('021-12345678')
                                    ->tel()
                                    ->maxLength(20)
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText('Nomor telepon kontak untuk pengunjung'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Section::make('📱 Media Sosial')
                            ->description('Atur tautan ke akun media sosial resmi aplikasi.')
                            ->icon('heroicon-o-chat-bubble-bottom-center')
                            ->schema([
                                TextInput::make('facebook_url')
                                    ->label('Facebook URL')
                                    ->placeholder('https://facebook.com/...')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->helperText('Tautan lengkap ke halaman Facebook resmi'),

                                TextInput::make('twitter_url')
                                    ->label('Twitter URL')
                                    ->placeholder('https://twitter.com/...')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->helperText('Tautan lengkap ke profil Twitter resmi'),

                                TextInput::make('instagram_url')
                                    ->label('Instagram URL')
                                    ->placeholder('https://instagram.com/...')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->helperText('Tautan lengkap ke profil Instagram resmi'),

                                TextInput::make('youtube_url')
                                    ->label('YouTube URL')
                                    ->placeholder('https://youtube.com/...')
                                    ->url()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-globe-alt')
                                    ->helperText('Tautan lengkap ke channel YouTube resmi'),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ]),

                    Grid::make([
                        'default' => 1,
                    ])
                    ->columnSpan([
                        'md' => 1,
                    ])
                    ->schema([
                        Section::make('🖼️ Logo & Favicon')
                            ->description('Upload logo dan favicon aplikasi.')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('app_logo')
                                    ->label('Logo Aplikasi')
                                    ->image()
                                    ->disk('public') // Explicitly set the disk
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'])
                                    ->maxSize(2048) // 2MB
                                    ->imageEditor()
                                    ->preserveFilenames()
                                    ->helperText('Format: JPG, PNG, SVG, WebP. Maksimal 2MB.')
                                    ->columnSpanFull(),

                                FileUpload::make('app_favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->disk('public') // Explicitly set the disk
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/jpeg'])
                                    ->maxSize(1024) // 1MB
                                    ->imageEditor()
                                    ->preserveFilenames()
                                    ->helperText('Format: ICO, PNG, JPG. Maksimal 1MB.')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ]),
                ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save settings using repository
        $settingsRepository = app(SettingsRepository::class);

        foreach ($data as $key => $value) {
            $settingsRepository->set($key, $value);
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan.')
            ->success()
            ->send();
    }

    protected function getSettingsData(): array
    {
        $settingsRepository = app(SettingsRepository::class);
        return $settingsRepository->all();
    }

    public function getTitle(): string
    {
        return 'Pengaturan Sistem';
    }

    public function getHeading(): string
    {
        return '⚙️ Pengaturan Sistem';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola konfigurasi aplikasi dan preferensi sistem';
    }
}
