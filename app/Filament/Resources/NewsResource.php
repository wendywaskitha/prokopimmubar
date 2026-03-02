<?php

namespace App\Filament\Resources;

use App\Models\Tag;
use Filament\Forms;
use App\Models\News;
use App\Models\User;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\NewsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\NewsResource\RelationManagers;

class NewsResource extends Resource implements HasShieldPermissions
{

    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Manajemen Konten';

    protected static ?string $modelLabel = 'Berita';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $user = Auth::user();

        // For kontributor, set status to draft by default and hide status field
        $isKontributor = $user->isKontributor();

        return $form
            ->schema([
                // Main content section (like WordPress editor area)
                Forms\Components\Section::make('📝 Konten Berita')
                    ->description('Area utama untuk menulis dan mengedit konten artikel berita Anda.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Berita')
                            ->placeholder('Masukkan judul berita yang menarik dan informatif...')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', str()->slug($state)) : null)
                            ->prefixIcon('heroicon-o-megaphone')
                            ->helperText('Judul akan otomatis menghasilkan slug URL. Gunakan kata kunci yang relevan untuk SEO.')
                            ->columnSpanFull(),

                        Forms\Components\MarkdownEditor::make('content')
                            ->label('Isi Berita')
                            ->placeholder('Tulis konten berita Anda di sini. Anda dapat menggunakan Markdown untuk formatting...')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Gunakan toolbar di atas untuk formatting. Pastikan konten informatif dan mudah dibaca.')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'heading',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'table',
                                'undo',
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->collapsible(),

                // Featured image metabox
                Forms\Components\Section::make('🖼️ Gambar Unggulan')
                    ->description('Upload gambar utama untuk berita.')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Upload Gambar')
                            ->image()
                            ->directory('news-images')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(3072) // 3MB
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Gambar utama yang akan muncul di thumbnail dan header berita. Format: JPG, PNG, WebP. Max 3MB.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // Additional content sections moved from sidebar
                Forms\Components\Section::make('🏷️ Kategorisasi & Metadata')
                    ->description('Atur kategori, tag, dan informasi tambahan untuk SEO dan organisasi konten.')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('categories')
                                    ->label('Kategori')
                                    ->placeholder('Pilih satu atau lebih kategori...')
                                    ->multiple()
                                    ->relationship('categories', 'name')
                                    ->options(Category::all()->pluck('name', 'id'))
                                    ->prefixIcon('heroicon-o-folder')
                                    ->helperText('Kategori membantu pembaca menemukan berita berdasarkan topik')
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->label('Nama Kategori'),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Deskripsi'),
                                    ]),

                                Forms\Components\Select::make('tags')
                                    ->label('Tag')
                                    ->placeholder('Pilih atau buat tag baru...')
                                    ->multiple()
                                    ->relationship('tags', 'name')
                                    ->options(Tag::all()->pluck('name', 'id'))
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->helperText('Tag membantu SEO dan memudahkan pencarian konten terkait')
                                    ->searchable()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->label('Nama Tag'),
                                    ]),
                            ]),

                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan/Excerpt')
                            ->placeholder('Tulis ringkasan singkat berita ini untuk preview dan SEO...')
                            ->rows(3)
                            ->maxLength(255)
                            ->helperText('Ringkasan akan muncul di daftar berita dan hasil pencarian (max 255 karakter)')
                            ->columnSpanFull()
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->placeholder('url-berita-ini')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn(string $operation): bool => $operation === 'edit')
                            ->prefixIcon('heroicon-o-link')
                            ->prefix('/')
                            ->suffix('.html')
                            ->helperText('URL ramah SEO, otomatis dibuat dari judul. Tidak dapat diubah setelah disimpan.')
                            ->columnSpanFull()
                            ->dehydrated(),
                    ])
                    ->columns(1)
                    ->columnSpan(['lg' => 8])
                    ->collapsible(),

                // Compact sidebar section
                Forms\Components\Group::make()
                    ->schema([
                        // Publish metabox
                        Forms\Components\Section::make('📤 Publikasi')
                            ->description('Atur status dan jadwal publikasi berita.')
                            ->icon('heroicon-o-rocket-launch')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status Publikasi')
                                    ->options(function () {
                                        $user = Auth::user();

                                        // Default options for all users
                                        $options = [
                                            'draft' => 'Draft',
                                            'archived' => 'Archived',
                                        ];

                                        // Only super admin and editor can publish directly
                                        if ($user->isSuperAdmin() || $user->isEditor()) {
                                            $options['published'] = 'Published';
                                        }

                                        return $options;
                                    })
                                    ->required()
                                    ->prefixIcon('heroicon-o-flag')
                                    ->helperText('Pilih status untuk mengontrol visibilitas berita')
                                    ->live()
                                    ->native(false)
                                    ->default($isKontributor ? 'draft' : null)
                                    ->disabled($isKontributor),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Tanggal & Waktu Publikasi')
                                    ->placeholder('Pilih waktu publikasi...')
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->helperText('Kosongkan untuk publikasi langsung saat status diubah ke Published')
                                    ->timezone('Asia/Makassar')
                                    ->disabled($isKontributor),

                                Forms\Components\Select::make('author_id')
                                    ->label('Penulis')
                                    ->placeholder('Pilih penulis berita...')
                                    ->relationship('author', 'name')
                                    ->options(User::all()->pluck('name', 'id'))
                                    ->required()
                                    ->prefixIcon('heroicon-o-user-circle')
                                    ->helperText('Penulis yang akan tercantum sebagai author berita')
                                    ->searchable()
                                    ->default($user->id)
                                    ->disabled(!$user->isSuperAdmin() && !$user->isEditor()),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('preview')
                                        ->label('👁️ Pratinjau')
                                        ->icon('heroicon-o-eye')
                                        ->color('info')
                                        ->url(fn($record) => route('news.preview', $record?->id ?? 0), true)
                                        ->openUrlInNewTab()
                                        ->tooltip('Lihat preview berita di tab baru'),
                                ])
                                    ->fullWidth(),
                            ])
                            ->columns(1)
                            ->collapsible(),


                    ])
                    ->columnSpan(['lg' => 4]),
            ])
            ->columns([
                'sm' => 1,
                'lg' => 12,
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isPenulis = $user->hasCustomRole('penulis');
        $isKontributor = $user->hasCustomRole('kontributor');

        // Scope the query based on user role
        if ($isPenulis) {
            // Penulis hanya bisa melihat berita mereka sendiri
            $table->modifyQueryUsing(fn(Builder $query) => $query->where('author_id', $user->id));
        } elseif ($isKontributor) {
            // Kontributor hanya bisa melihat berita mereka sendiri
            $table->modifyQueryUsing(fn(Builder $query) => $query->where('author_id', $user->id));
        }

        $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Gambar')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl('/images/placeholder-news.png')
                    ->tooltip('Gambar unggulan berita'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Berita')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn(News $record): string => Str::limit($record->excerpt, 100))
                    ->formatStateUsing(function (News $record) use ($user) {
                        // Tambahkan indikator untuk berita yang dibuat oleh user sendiri
                        if (($user->hasCustomRole('penulis') || $user->hasCustomRole('kontributor')) && $record->author_id === $user->id) {
                            return $record->title . ' 📝';
                        }
                        return $record->title;
                    }),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Penulis')
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-user-circle')
                    ->color('blue')
                    ->copyable()
                    ->tooltip('Penulis berita'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->icon(fn(string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil-square',
                        'published' => 'heroicon-o-check-circle',
                        'archived' => 'heroicon-o-archive-box',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'danger',
                        default => 'warning',
                    })
                    ->tooltip(fn(string $state): string => match ($state) {
                        'draft' => 'Masih dalam tahap draft',
                        'published' => 'Sudah dipublikasikan',
                        'archived' => 'Diarsipkan/tidak aktif',
                        default => 'Status tidak diketahui',
                    }),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->separator(', ')
                    ->limit(2)
                    ->tooltip('Kategori berita')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->tooltip('Tanggal dan waktu publikasi')
                    ->placeholder('Belum dijadwalkan'),

                Tables\Columns\TextColumn::make('excerpt')
                    ->label('Ringkasan')
                    ->limit(60)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 60 ? $state : null;
                    })
                    ->placeholder('Tidak ada ringkasan')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->tooltip('Tanggal pembuatan berita')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->multiple()
                    ->placeholder('Semua status'),

                Tables\Filters\SelectFilter::make('author')
                    ->label('Filter Penulis')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua penulis')
                    ->visible($user->isSuperAdmin() || $user->isEditor()),

                Tables\Filters\SelectFilter::make('categories')
                    ->label('Filter Kategori')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua kategori'),

                Tables\Filters\Filter::make('published_today')
                    ->label('Dipublikasi Hari Ini')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereDate('published_at', today())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Berita Terbaru (7 hari)')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('created_at', '>=', now()->subDays(7))
                    )
                    ->toggle(),
            ]);

        // For penulis, only show their own news
        if ($isPenulis) {
            $table->modifyQueryUsing(fn(Builder $query) => $query->where('author_id', $user->id));
        }

        // For kontributor, only show their own news
        if ($isKontributor) {
            $table->modifyQueryUsing(fn(Builder $query) => $query->where('author_id', $user->id));
        }

        // For super_admin and editor, show all news (no query modification needed)
        // Only apply author filter for penulis and kontributor

        // Define actions based on user role
        $actions = [
            Tables\Actions\ViewAction::make()
                ->icon('heroicon-o-eye')
                ->tooltip('Lihat detail berita'),
        ];

        // Editors and above can edit
        if ($user->isEditor() || $user->isSuperAdmin()) {
            $actions[] = Tables\Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->tooltip('Edit berita');
        } elseif ($isPenulis) {
            // Penulis can only edit their own news
            $actions[] = Tables\Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->tooltip('Edit berita')
                ->visible(fn($record) => $record->author_id === $user->id);
        } elseif ($isKontributor) {
            // Kontributor can only edit their own draft news
            $actions[] = Tables\Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->tooltip('Edit berita')
                ->visible(fn($record) => $record->author_id === $user->id && $record->status === 'draft');
        }

        // Add preview action for published news
        $actions[] = Tables\Actions\Action::make('preview')
            ->label('Preview')
            ->icon('heroicon-o-magnifying-glass-plus')
            ->color('info')
            // ->url(fn($record) => route('news.show', $record->slug))
            ->openUrlInNewTab()
            ->visible(fn($record) => $record->status === 'published')
            ->tooltip('Lihat berita di website');

        // Editors and above can publish
        if ($user->isEditor() || $user->isSuperAdmin()) {
            $actions[] = Tables\Actions\Action::make('quick_publish')
                ->label('Publikasikan')
                ->icon('heroicon-o-rocket-launch')
                ->color('success')
                ->action(fn($record) => $record->update(['status' => 'published', 'published_at' => now()]))
                ->visible(fn($record) => $record->status === 'draft')
                ->requiresConfirmation()
                ->tooltip('Publikasikan berita langsung');
        }

        // Define delete action based on user role
        if ($user->isEditor() || $user->isSuperAdmin()) {
            $actions[] = Tables\Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->tooltip('Hapus berita')
                ->requiresConfirmation()
                ->modalHeading('Hapus Berita')
                ->modalDescription('Apakah Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus');
        } elseif ($isPenulis) {
            // Penulis can only delete their own news
            $actions[] = Tables\Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->tooltip('Hapus berita')
                ->requiresConfirmation()
                ->modalHeading('Hapus Berita')
                ->modalDescription('Apakah Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->visible(fn($record) => $record->author_id === $user->id);
        } elseif ($isKontributor) {
            // Kontributor can only delete their own draft news
            $actions[] = Tables\Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->tooltip('Hapus berita')
                ->requiresConfirmation()
                ->modalHeading('Hapus Berita')
                ->modalDescription('Apakah Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->visible(fn($record) => $record->author_id === $user->id && $record->status === 'draft');
        }

        $table->actions($actions);

        // Define bulk actions based on user role
        $bulkActions = [
            Tables\Actions\BulkActionGroup::make([]),
        ];

        if ($user->isEditor() || $user->isSuperAdmin()) {
            $bulkActions[0]->actions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Berita Terpilih')
                    ->modalDescription('Semua berita yang dipilih akan dihapus permanen.')
                    ->modalSubmitActionLabel('Ya, Hapus Semua'),

                Tables\Actions\BulkAction::make('publish_all')
                    ->label('Publikasikan Semua')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->action(fn($records) => $records->each->update(['status' => 'published', 'published_at' => now()]))
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation(),

                Tables\Actions\BulkAction::make('archive_all')
                    ->label('Arsipkan Semua')
                    ->icon('heroicon-o-archive-box')
                    ->color('danger')
                    ->action(fn($records) => $records->each->update(['status' => 'archived']))
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation(),
            ]);
        }

        $table->bulkActions($bulkActions);

        // Hanya super admin dan editor yang bisa melakukan bulk actions
        if (!($user->can('delete_any_news') || $user->hasCustomRole('editor'))) {
            $table->bulkActions([]);
        }

        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Berita')
            ->emptyStateDescription('Mulai dengan membuat artikel berita pertama untuk website Anda.')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Berita Pertama')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    // public static function canViewAny(): bool
    // {
    //     $user = Auth::user();
    //     return $user->can('view_any_news') ||
    //            $user->hasRole('penulis') ||
    //            $user->hasRole('kontributor') ||
    //            $user->hasRole('super_admin');
    // }

    // public static function canCreate(): bool
    // {

    //     $user = Auth::user();
    //     return $user->can('create_news') ||
    //            $user->hasRole('editor') ||
    //            $user->hasRole('penulis') ||
    //            $user->hasRole('super_admin');
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish'
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
