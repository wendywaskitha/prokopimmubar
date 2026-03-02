<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Manajemen Konten';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📂 Informasi Kategori')
                    ->description('Buat dan kelola kategori untuk mengorganisir konten dengan lebih baik.')
                    ->icon('heroicon-o-folder-open')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->placeholder('Contoh: Berita Terkini, Teknologi, Olahraga')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                            ->prefixIcon('heroicon-o-tag')
                            ->helperText('Nama kategori akan muncul di menu navigasi dan halaman konten'),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->placeholder('url-friendly-name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn(string $operation): bool => $operation === 'edit')
                            ->prefixIcon('heroicon-o-link')
                            ->helperText('URL yang ramah SEO, otomatis dibuat dari nama kategori')
                            ->prefix('/')
                            ->suffix('.html'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Kategori')
                            ->placeholder('Jelaskan apa saja konten yang masuk dalam kategori ini...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Deskripsi singkat tentang kategori ini (opsional, untuk SEO dan informasi pengguna)'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-tag')
                    ->copyable()
                    ->copyMessage('Nama kategori berhasil disalin!')
                    ->tooltip('Klik untuk menyalin nama kategori'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('URL Slug')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->icon('heroicon-o-link')
                    ->prefix('/')
                    ->suffix('.html')
                    ->copyable()
                    ->copyMessage('URL slug berhasil disalin!')
                    ->fontFamily('mono')
                    ->tooltip('URL yang digunakan untuk mengakses kategori'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->placeholder('Tidak ada deskripsi')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('news_count')
                    ->label('Jumlah Berita')
                    ->counts('news')
                    ->icon('heroicon-o-newspaper')
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->tooltip('Tanggal dan waktu kategori dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->tooltip('Terakhir kali kategori diubah')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('recent')
                    ->label('Kategori Terbaru')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),

                Tables\Filters\Filter::make('has_description')
                    ->label('Memiliki Deskripsi')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('description')->where('description', '!=', ''))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail kategori'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit kategori'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus kategori')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Kategori')
                    ->modalDescription('Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kategori Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua kategori yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Kategori')
            ->emptyStateDescription('Mulai dengan membuat kategori pertama untuk mengorganisir konten Anda.')
            ->emptyStateIcon('heroicon-o-tag');
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            // 'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
