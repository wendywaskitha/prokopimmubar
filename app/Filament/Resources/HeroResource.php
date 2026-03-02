<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroResource\Pages;
use App\Filament\Resources\HeroResource\RelationManagers;
use App\Models\Hero;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class HeroResource extends Resource
{

    protected static ?string $model = Hero::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Manajemen Konten';

    protected static ?string $modelLabel = 'Slide Banner';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('🎯 Informasi Hero')
                    ->description('Buat slide hero yang menarik untuk halaman utama website dengan gambar berkualitas tinggi dan pesan yang kuat.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Hero')
                            ->placeholder('Contoh: Selamat Datang di Website Resmi Kami')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-megaphone')
                            ->helperText('Judul utama yang akan muncul besar di slide hero (maksimal 255 karakter)')
                            ->live(onBlur: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi/Subtitle')
                            ->placeholder('Tuliskan pesan pendukung atau call-to-action yang menarik...')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Deskripsi pendukung atau call-to-action (maksimal 500 karakter)')
                            ->live(onBlur: true),

                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Hero')
                            ->image()
                            ->directory('hero-images')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120) // 5MB
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                            ])
                            ->helperText('Upload gambar berkualitas tinggi. Format: JPG, PNG, WebP. Ukuran ideal: 1920x1080px. Maksimal 5MB.')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('link')
                            ->label('URL Tujuan')
                            ->placeholder('https://example.com/halaman-tujuan')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-link')
                            ->helperText('Tautan yang akan dibuka ketika hero diklik (opsional)')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('test_link')
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->visible(fn ($state) => filled($state))
                            ),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('⚙️ Pengaturan Tampilan')
                    ->description('Konfigurasi status dan urutan tampilan hero di carousel.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->onIcon('heroicon-o-eye')
                            ->offIcon('heroicon-o-eye-slash')
                            ->onColor('success')
                            ->offColor('danger')
                            ->helperText('Aktifkan untuk menampilkan hero di carousel website')
                            ->inline(false),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->placeholder('0')
                            ->integer()
                            ->default(0)
                            ->minValue(0)
                            ->step(1)
                            ->prefixIcon('heroicon-o-bars-arrow-up')
                            ->helperText('Angka kecil = tampil lebih dulu dalam carousel (0 = pertama)')
                            ->suffix('posisi'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Preview')
                    ->size(100)
                    ->circular()
                    ->defaultImageUrl('/images/placeholder-hero.png')
                    ->tooltip('Preview gambar hero'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Hero')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-megaphone')
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    })
                    ->copyable()
                    ->copyMessage('Judul berhasil disalin!'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->limit(50)
                    ->color('gray')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->placeholder('Tidak ada deskripsi')
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\TextColumn::make('link')
                    ->label('URL Tujuan')
                    ->searchable()
                    ->limit(30)
                    ->color('blue')
                    ->icon('heroicon-o-link')
                    ->copyable()
                    ->copyMessage('URL berhasil disalin!')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->placeholder('Tidak ada link')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->tooltip(fn ($state) => $state ? 'Hero aktif dan tampil' : 'Hero tidak aktif'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter()
                    ->icon('heroicon-o-bars-arrow-up')
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => 'Pos. ' . ($state + 1))
                    ->tooltip('Posisi dalam carousel'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->tooltip('Tanggal dan waktu hero dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Hero Aktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),

                Tables\Filters\Filter::make('is_inactive')
                    ->label('Hero Nonaktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false))
                    ->toggle(),

                Tables\Filters\Filter::make('has_link')
                    ->label('Memiliki Link')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('link')->where('link', '!=', ''))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Hero Terbaru (7 hari)')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail hero'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit hero'),

                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-magnifying-glass-plus')
                    ->color('info')
                    ->url(fn ($record) => $record->link ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->link))
                    ->tooltip('Buka link tujuan hero'),

                Tables\Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active]))
                    ->requiresConfirmation()
                    ->tooltip(fn ($record) => $record->is_active ? 'Sembunyikan dari carousel' : 'Tampilkan di carousel'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus hero')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Hero')
                    ->modalDescription('Apakah Anda yakin ingin menghapus hero ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Hero Terpilih')
                        ->modalDescription('Semua hero yang dipilih akan dihapus permanen.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),

                    Tables\Actions\BulkAction::make('activate_all')
                        ->label('Aktifkan Semua')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->tooltip('Aktifkan semua hero yang dipilih'),

                    Tables\Actions\BulkAction::make('deactivate_all')
                        ->label('Nonaktifkan Semua')
                        ->icon('heroicon-o-eye-slash')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->tooltip('Nonaktifkan semua hero yang dipilih'),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->emptyStateHeading('Belum Ada Hero')
            ->emptyStateDescription('Mulai dengan membuat slide hero pertama untuk carousel halaman utama website.')
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Hero Pertama')
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
            'index' => Pages\ListHeroes::route('/'),
            'create' => Pages\CreateHero::route('/create'),
            'edit' => Pages\EditHero::route('/{record}/edit'),
        ];
    }
}
