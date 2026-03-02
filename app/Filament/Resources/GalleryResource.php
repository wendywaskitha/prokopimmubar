<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\News;
use Filament\Tables;
use App\Models\Gallery;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleBasedResource;
use App\Filament\Resources\GalleryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GalleryResource\RelationManagers;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class GalleryResource extends Resource
{

    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Media Sosial';

    protected static ?string $modelLabel = 'Galeri';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📸 Informasi Galeri')
                    ->description('Buat galeri foto untuk melengkapi artikel berita dengan koleksi gambar yang menarik.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Galeri')
                            ->placeholder('Contoh: Galeri Kegiatan Gotong Royong RT 05')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-document-text')
                            ->helperText('Judul yang menarik dan deskriptif untuk galeri foto'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Galeri')
                            ->placeholder('Jelaskan konteks atau latar belakang dari kumpulan foto ini...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Deskripsi singkat tentang tema atau acara dalam galeri (opsional)'),

                        Forms\Components\Select::make('news_id')
                            ->label('Berita Terkait')
                            ->placeholder('Pilih artikel berita yang sesuai...')
                            ->required()
                            ->relationship('news', 'title')
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-newspaper')
                            ->helperText('Hubungkan galeri dengan artikel berita untuk konteks yang lebih lengkap')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->label('Judul Berita'),
                                Forms\Components\Textarea::make('content')
                                    ->required()
                                    ->label('Konten Berita'),
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Set kategori berdasarkan berita yang dipilih
                                if ($state) {
                                    $news = News::find($state);
                                    if ($news) {
                                        // Kita tidak perlu menyimpan kategori secara eksplisit karena akan diambil dari berita
                                    }
                                }
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('🖼️ Koleksi Gambar')
                    ->description('Upload dan atur gambar-gambar yang akan ditampilkan dalam galeri.')
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\FileUpload::make('image_path')
                                            ->label('Upload Gambar')
                                            ->image()
                                            ->directory('galleries')
                                            ->required()
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '16:9',
                                                '4:3',
                                                '1:1',
                                            ])
                                            ->helperText('Format: JPG, PNG, WebP. Maksimal 2MB')
                                            ->columnSpan(1),

                                        Forms\Components\Textarea::make('caption')
                                            ->label('Caption/Keterangan')
                                            ->placeholder('Deskripsi singkat tentang gambar ini...')
                                            ->rows(3)
                                            ->helperText('Keterangan yang akan muncul di bawah gambar')
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Urutan Tampil')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->step(1)
                                            ->prefixIcon('heroicon-o-bars-3')
                                            ->helperText('Angka kecil = tampil lebih dulu')
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->orderColumn('sort_order')
                            ->collapsible()
                            ->cloneable()
                            ->reorderableWithButtons()
                            ->itemLabel(fn (array $state): ?string =>
                                !empty($state['caption'])
                                    ? 'Gambar: ' . Str::limit($state['caption'], 30)
                                    : 'Gambar tanpa caption'
                            )
                            ->addActionLabel('➕ Tambah Gambar Baru')
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) => $action
                                    ->requiresConfirmation()
                                    ->modalHeading('Hapus Gambar')
                                    ->modalDescription('Apakah Anda yakin ingin menghapus gambar ini dari galeri?')
                            )
                            ->label('Daftar Gambar')
                            ->columnSpanFull()
                            ->minItems(1)
                            ->maxItems(20),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Galeri')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-photo')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('news.title')
                    ->label('Berita Terkait')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-newspaper')
                    ->color('blue')
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    })
                    // ->url(fn ($record) => $record->news ? route('filament.admin.resources.news.view', $record->news) : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('news.categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->separator(',')
                    ->color('success')
                    ->icon('heroicon-o-tag'),

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

                Tables\Columns\TextColumn::make('images_count')
                    ->label('Jumlah Foto')
                    ->counts('images')
                    ->icon('heroicon-o-squares-2x2')
                    ->alignCenter()
                    ->sortable()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state . ' foto')
                    ->tooltip('Total gambar dalam galeri'),

                Tables\Columns\ImageColumn::make('images.image_path')
                    ->label('Preview')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->tooltip('Preview gambar dalam galeri'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->tooltip('Waktu galeri dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->tooltip('Terakhir kali galeri diperbarui')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('news_id')
                    ->label('Filter Berita')
                    ->relationship('news', 'title')
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih berita tertentu'),

                Tables\Filters\SelectFilter::make('news.categories')
                    ->label('Filter Kategori')
                    ->relationship('news.categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih kategori'),

                Tables\Filters\Filter::make('has_description')
                    ->label('Memiliki Deskripsi')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNotNull('description')->where('description', '!=', '')
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Galeri Terbaru (7 hari)')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('created_at', '>=', now()->subDays(7))
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('has_many_images')
                    ->label('Galeri Lengkap (5+ foto)')
                    ->query(fn (Builder $query): Builder =>
                        $query->has('images', '>=', 5)
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail galeri'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit galeri dan gambar'),

                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-magnifying-glass-plus')
                    ->color('info')
                    ->url(fn ($record) => '#') // Add your preview URL here
                    ->openUrlInNewTab()
                    ->tooltip('Lihat tampilan galeri di website'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus galeri')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Galeri')
                    ->modalDescription('Semua gambar dalam galeri ini juga akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus Galeri'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Galeri Terpilih')
                        ->modalDescription('Semua galeri dan gambar yang dipilih akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.'),

                    Tables\Actions\BulkAction::make('export_images')
                        ->label('Export Gambar')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Add export logic here
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Galeri')
            ->emptyStateDescription('Mulai dengan membuat galeri foto pertama untuk melengkapi artikel berita Anda.')
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Galeri Pertama')
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'view' => Pages\ViewGallery::route('/{record}'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
