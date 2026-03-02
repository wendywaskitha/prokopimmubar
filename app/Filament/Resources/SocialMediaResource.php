<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialMediaResource\Pages;
use App\Filament\Resources\SocialMediaResource\RelationManagers;
use App\Models\SocialMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class SocialMediaResource extends Resource
{

    protected static ?string $model = SocialMedia::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Media Sosial';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📱 Informasi Media Sosial')
                    ->description('Tambahkan konten media sosial untuk ditampilkan di website atau dashboard.')
                    ->icon('heroicon-o-share')
                    ->schema([
                        Forms\Components\Select::make('platform')
                            ->label('Platform Media Sosial')
                            ->placeholder('Pilih platform...')
                            ->options([
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter',
                                'instagram' => 'Instagram',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-device-phone-mobile')
                            ->helperText('Pilih platform tempat konten ini berasal')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // You can add platform-specific logic here if needed
                            }),

                        Forms\Components\TextInput::make('url')
                            ->label('URL/Tautan')
                            ->placeholder('https://youtube.com/watch?v=...')
                            ->required()
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-link')
                            ->helperText('Masukkan URL lengkap dari konten media sosial')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('test_url')
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->tooltip('Buka tautan')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->visible(fn ($state) => filled($state))
                            ),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Konten')
                            ->placeholder('Contoh: Tutorial Laravel Terbaru')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-document-text')
                            ->helperText('Judul yang menggambarkan konten (opsional)')
                            ->live(onBlur: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Jelaskan singkat tentang konten ini...')
                            ->rows(3)
                            ->helperText('Deskripsi singkat untuk memberikan konteks kepada pengunjung')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('🖼️ Media & Preview')
                    ->description('Upload thumbnail dan lihat preview dari konten media sosial.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Thumbnail/Gambar Preview')
                            ->image()
                            ->directory('social-media-thumbnails')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048) // 2MB
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Upload gambar thumbnail untuk preview konten. Format: JPG, PNG, WebP. Max 2MB.')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('preview')
                            ->label('🔗 Preview Tautan')
                            ->content(fn ($record) => $record
                                ? new \Illuminate\Support\HtmlString('<div class="text-sm"><strong>URL:</strong> <a href="' . $record->url . '" target="_blank" class="text-blue-600 hover:underline">' . $record->url . '</a></div>')
                                : 'Tautan media akan muncul di sini setelah disimpan'
                            )
                            ->helperText('Preview tautan yang akan digunakan untuk mengakses konten')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl('/images/placeholder-social.png')
                    ->tooltip('Preview thumbnail konten'),

                Tables\Columns\BadgeColumn::make('platform')
                    ->label('Platform')
                    ->searchable()
                    ->sortable()
                    ->icon(fn (string $state): string => match ($state) {
                        'youtube' => 'heroicon-o-play-circle',
                        'tiktok' => 'heroicon-o-musical-note',
                        'facebook' => 'heroicon-o-user-group',
                        'twitter' => 'heroicon-o-chat-bubble-left-ellipsis',
                        'instagram' => 'heroicon-o-camera',
                        default => 'heroicon-o-globe-alt',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'youtube' => 'danger',
                        'tiktok' => 'pink',
                        'facebook' => 'info',
                        'twitter' => 'primary',
                        'instagram' => 'warning',
                        default => 'gray',
                    })
                    ->tooltip(fn (string $state): string => 'Konten dari platform ' . ucfirst($state)),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Konten')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(50)
                    ->wrap()
                    ->icon('heroicon-o-document-text')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->placeholder('Tidak ada judul')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 60 ? $state : null;
                    })
                    ->placeholder('Tidak ada deskripsi')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('url')
                    ->label('Tautan')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->url)
                    ->icon('heroicon-o-link')
                    ->color('blue')
                    ->copyable()
                    ->copyMessage('URL berhasil disalin!')
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->tooltip('Tanggal konten ditambahkan')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Filter Platform')
                    ->options([
                        'youtube' => 'YouTube',
                        'tiktok' => 'TikTok',
                        'facebook' => 'Facebook',
                        'twitter' => 'Twitter',
                        'instagram' => 'Instagram',
                    ])
                    ->multiple()
                    ->placeholder('Semua platform'),

                Tables\Filters\Filter::make('has_thumbnail')
                    ->label('Memiliki Thumbnail')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('thumbnail'))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Konten Terbaru (7 hari)')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail konten'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit konten media sosial'),

                Tables\Actions\Action::make('visit_url')
                    ->label('Kunjungi')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab()
                    ->tooltip('Buka tautan di tab baru'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus konten')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Konten Media Sosial')
                    ->modalDescription('Apakah Anda yakin ingin menghapus konten ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Konten Terpilih')
                        ->modalDescription('Semua konten media sosial yang dipilih akan dihapus permanen.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),

                    Tables\Actions\BulkAction::make('export_urls')
                        ->label('Export URL')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Add export logic here
                        })
                        ->deselectRecordsAfterCompletion()
                        ->tooltip('Export semua URL yang dipilih'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Konten Media Sosial')
            ->emptyStateDescription('Mulai dengan menambahkan konten media sosial pertama untuk ditampilkan di website.')
            ->emptyStateIcon('heroicon-o-share')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Konten Pertama')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialMedia::route('/'),
            'create' => Pages\CreateSocialMedia::route('/create'),
            // 'view' => Pages\ViewSocialMedia::route('/{record}'),
            'edit' => Pages\EditSocialMedia::route('/{record}/edit'),
        ];
    }
}
