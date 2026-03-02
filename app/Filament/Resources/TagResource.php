<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class TagResource extends Resource
{

    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Manajemen Konten';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('🏷️ Informasi Tag')
                    ->description('Buat dan kelola tag untuk membantu kategorisasi konten dan meningkatkan SEO website.')
                    ->icon('heroicon-o-hashtag')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tag')
                            ->placeholder('Contoh: Laravel, PHP, Tutorial, Tips')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                            ->prefixIcon('heroicon-o-hashtag')
                            ->helperText('Nama tag akan muncul di halaman konten dan membantu pengunjung menemukan topik terkait')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('suggest')
                                    ->icon('heroicon-o-light-bulb')
                                    ->tooltip('Saran: Gunakan kata kunci yang sering dicari')
                                    ->color('warning')
                                    ->action(function () {
                                        // You can add tag suggestion logic here
                                    })
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->placeholder('url-friendly-tag-name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->prefixIcon('heroicon-o-link')
                            ->helperText('URL ramah SEO, otomatis dibuat dari nama tag. Tidak dapat diubah setelah disimpan.')
                            ->prefix('#')
                            ->dehydrated(),
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
                    ->label('Nama Tag')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-hashtag')
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Nama tag berhasil disalin!')
                    ->tooltip('Klik untuk menyalin nama tag')
                    ->prefix('#')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('slug')
                    ->label('URL Slug')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->icon('heroicon-o-link')
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('URL slug berhasil disalin!')
                    ->tooltip('URL yang digunakan untuk mengakses halaman tag')
                    ->prefix('/')
                    ->suffix('.html'),

                Tables\Columns\TextColumn::make('news_count')
                    ->label('Jumlah Berita')
                    ->counts('news')
                    ->alignCenter()
                    ->sortable()
                    ->icon('heroicon-o-newspaper')
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state . ' artikel')
                    ->tooltip('Total berita yang menggunakan tag ini'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->tooltip('Tanggal dan waktu tag dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->tooltip('Terakhir kali tag diubah')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('popular_tags')
                    ->label('Tag Populer (5+ artikel)')
                    ->query(fn (Builder $query): Builder => $query->has('news', '>=', 5))
                    ->toggle(),

                Tables\Filters\Filter::make('unused_tags')
                    ->label('Tag Tidak Digunakan')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('news'))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Tag Terbaru (7 hari)')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail tag'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit tag'),

                Tables\Actions\Action::make('view_articles')
                    ->label('Lihat Artikel')
                    ->icon('heroicon-o-newspaper')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.news.index', ['tableFilters[tags][values][0]' => $record->id]))
                    ->tooltip('Lihat semua artikel dengan tag ini'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus tag')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Tag')
                    ->modalDescription(function ($record) {
                        $count = $record->news()->count();
                        if ($count > 0) {
                            return "Tag ini digunakan oleh {$count} artikel. Menghapus tag akan menghilangkannya dari semua artikel terkait. Apakah Anda yakin?";
                        }
                        return 'Apakah Anda yakin ingin menghapus tag ini?';
                    })
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Tag Terpilih')
                        ->modalDescription('Semua tag yang dipilih akan dihapus dan akan hilang dari artikel yang menggunakannya. Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),

                    Tables\Actions\BulkAction::make('export_tags')
                        ->label('Export Tag')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Add export logic here
                        })
                        ->deselectRecordsAfterCompletion()
                        ->tooltip('Export daftar tag yang dipilih'),

                    Tables\Actions\BulkAction::make('merge_tags')
                        ->label('Gabung Tag')
                        ->icon('heroicon-o-arrows-pointing-in')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('target_tag')
                                ->label('Gabung ke Tag')
                                ->options(Tag::all()->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function ($records, $data) {
                            // Add merge logic here
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->tooltip('Gabungkan beberapa tag menjadi satu'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Tag')
            ->emptyStateDescription('Mulai dengan membuat tag pertama untuk membantu mengorganisir dan mengategorikan konten website.')
            ->emptyStateIcon('heroicon-o-hashtag')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Tag Pertama')
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            // 'view' => Pages\ViewTag::route('/{record}'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
