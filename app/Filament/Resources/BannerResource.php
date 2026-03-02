<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{

    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Manajemen Konten';

    protected static ?int $navigationSort = 6;

    protected static ?string $label = 'Banner';

    protected static ?string $pluralLabel = 'Banner';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📄 Informasi Banner')
                    ->description(function ($get) {
                        if ($get('position') === 'opd-head-greeting') {
                            return 'Isi data banner ucapan Kepala OPD. Pastikan gambar berkualitas tinggi dan sesuai dengan tema ucapan.';
                        }
                        return 'Isi data banner yang akan ditampilkan pada website. Pastikan gambar berkualitas tinggi dan judul menarik perhatian.';
                    })
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Banner')
                                    ->placeholder(function ($get) {
                                        if ($get('position') === 'opd-head-greeting') {
                                            return 'Contoh: Selamat Datang di Kabupaten Muna Barat';
                                        }
                                        return 'Contoh: Promo Spesial Akhir Tahun 2025!';
                                    })
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->helperText(function ($get) {
                                        if ($get('position') === 'opd-head-greeting') {
                                            return 'Judul ucapan Kepala OPD (maksimal 255 karakter)';
                                        }
                                        return 'Gunakan kata-kata yang menarik dan mudah diingat (maksimal 255 karakter)';
                                    })
                                    ->prefixIcon('heroicon-o-pencil-square')
                                    ->columnSpan(2),

                                Forms\Components\FileUpload::make('image')
                                    ->label('Gambar Banner')
                                    ->image()
                                    ->directory('banners')
                                    ->required()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(2048) // 2MB
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->helperText(function ($get) {
                                        return $get('size') === 'card-1x1' 
                                            ? 'Upload gambar dengan format JPG/PNG/WebP. Ukuran ideal: 1:1 (square). Maksimal 2MB.'
                                            : 'Upload gambar dengan format JPG/PNG/WebP. Ukuran ideal: 1200x400px (rasio 3:1). Maksimal 2MB.';
                                    })
                                    ->hintIcon('heroicon-o-information-circle')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('link')
                                    ->label('URL Tujuan')
                                    ->placeholder('https://example.com/promo')
                                    ->url()
                                    ->maxLength(255)
                                    ->suffixIcon('heroicon-o-link')
                                    ->helperText(function ($get) {
                                        if ($get('position') === 'opd-head-greeting') {
                                            return 'Tautan yang akan dibuka ketika banner ucapan diklik. Kosongkan jika tidak diperlukan.';
                                        }
                                        return 'Tautan yang akan dibuka ketika banner diklik. Kosongkan jika tidak diperlukan.';
                                    })
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Forms\Components\Section::make('⚙️ Pengaturan Display')
                    ->description(function ($get) {
                        if ($get('position') === 'opd-head-greeting') {
                            return 'Konfigurasi tampilan dan jadwal tayang banner ucapan Kepala OPD.';
                        }
                        return 'Konfigurasi tampilan, penempatan, dan jadwal tayang banner.';
                    })
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('position')
                                    ->label('Posisi Banner')
                                    ->options(Banner::POSITIONS)
                                    ->required()
                                    ->searchable()
                                    ->helperText(function ($get) {
                                        if ($get('position') === 'opd-head-greeting') {
                                            return 'Posisi khusus untuk slide ucapan Kepala OPD';
                                        }
                                        return 'Pilih lokasi penempatan banner di halaman website';
                                    })
                                    ->prefixIcon('heroicon-o-map-pin'),

                                Forms\Components\Select::make('size')
                                    ->label('Ukuran Tampilan')
                                    ->options(Banner::SIZES)
                                    ->default('responsive')
                                    ->required()
                                    ->searchable()
                                    ->helperText(function ($get) {
                                        if ($get('position') === 'opd-head-greeting') {
                                            return 'Pilih ukuran card 1x1 untuk ucapan Kepala OPD';
                                        }
                                        return 'Responsive direkomendasikan untuk semua perangkat';
                                    })
                                    ->prefixIcon('heroicon-o-squares-2x2'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->onIcon('heroicon-o-eye')
                                    ->offIcon('heroicon-o-eye-slash')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Aktifkan untuk menampilkan banner di website'),
                            ]),

                        Forms\Components\Fieldset::make('Jadwal Tayang')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('start_date')
                                            ->label('Tanggal Mulai')
                                            ->placeholder('Pilih tanggal mulai tayang')
                                            ->native(false)
                                            ->displayFormat('d F Y')
                                            ->prefixIcon('heroicon-o-calendar')
                                            ->helperText('Kosongkan untuk mulai tayang segera'),

                                        Forms\Components\DatePicker::make('end_date')
                                            ->label('Tanggal Selesai')
                                            ->placeholder('Pilih tanggal selesai tayang')
                                            ->native(false)
                                            ->displayFormat('d F Y')
                                            ->prefixIcon('heroicon-o-calendar')
                                            ->helperText('Kosongkan untuk tayang tanpa batas waktu')
                                            ->after('start_date'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Forms\Components\Section::make('📊 Informasi Statistik')
                    ->description('Data analitik banner (hanya untuk edit)')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Placeholder::make('click_count')
                            ->label('Total Klik')
                            ->content(fn ($record) => $record ? number_format($record->click_count ?? 0) . ' klik' : 'Banner baru - belum ada data')
                            ->helperText('Jumlah klik yang tercatat sejak banner aktif'),

                        Forms\Components\Placeholder::make('created_info')
                            ->label('Informasi Pembuatan')
                            ->content(fn ($record) => $record
                                ? 'Dibuat pada ' . $record->created_at?->format('d F Y, H:i') . ' WIB'
                                : 'Banner baru akan dibuat setelah disimpan'
                            ),
                    ])
                    ->visible(fn ($record) => $record !== null)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Preview')
                    ->size(80)
                    ->circular()
                    ->defaultImageUrl('/images/placeholder-banner.png'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\BadgeColumn::make('position')
                    ->label('Posisi')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => Banner::POSITIONS[$state] ?? ucfirst($state))
                    ->colors([
                        'primary' => 'header',
                        'success' => 'sidebar',
                        'warning' => 'footer',
                        'info' => 'content',
                        'danger' => 'opd-head-greeting',
                    ]),

                Tables\Columns\BadgeColumn::make('size')
                    ->label('Ukuran')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => Banner::SIZES[$state] ?? ucfirst($state))
                    ->colors([
                        'success' => 'responsive',
                        'warning' => 'large',
                        'info' => 'medium',
                        'gray' => 'small',
                        'primary' => 'card-1x1',
                    ]),

                Tables\Columns\TextColumn::make('link')
                    ->label('Tautan')
                    ->searchable()
                    ->limit(30)
                    ->copyable()
                    ->copyMessage('URL berhasil disalin!')
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Segera')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Tanpa batas')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('click_count')
                    ->label('Klik')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->label('Posisi')
                    ->options(Banner::POSITIONS)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('size')
                    ->label('Ukuran')
                    ->options(Banner::SIZES)
                    ->multiple(),

                Tables\Filters\Filter::make('is_active')
                    ->label('Banner Aktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),

                Tables\Filters\Filter::make('is_inactive')
                    ->label('Banner Nonaktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false))
                    ->toggle(),

                Tables\Filters\Filter::make('scheduled')
                    ->label('Terjadwal')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNotNull('start_date')->orWhereNotNull('end_date')
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
