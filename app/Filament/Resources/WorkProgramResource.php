<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkProgramResource\Pages;
use App\Filament\Resources\WorkProgramResource\RelationManagers;
use App\Models\User;
use App\Models\WorkProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class WorkProgramResource extends Resource
{

    protected static ?string $model = WorkProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Manajemen Konten';

    protected static ?string $modelLabel = 'Program Kerja';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('📋 Informasi Program')
                    ->description('Masukkan informasi dasar tentang program kerja yang akan dilaksanakan.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Program Kerja')
                            ->placeholder('Contoh: Pembangunan Jalan Desa Tahap 1')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-megaphone')
                            ->helperText('Nama program yang jelas dan deskriptif untuk memudahkan identifikasi')
                            ->live(onBlur: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Program')
                            ->placeholder('Jelaskan tujuan, sasaran, dan manfaat program kerja ini secara detail...')
                            ->required()
                            ->rows(4)
                            ->helperText('Uraian lengkap tentang program, target yang ingin dicapai, dan dampak yang diharapkan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('⚙️ Detail Program')
                    ->description('Tentukan kategori, status, prioritas, dan timeline pelaksanaan program.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Kategori Program')
                            ->placeholder('Pilih kategori yang sesuai...')
                            ->options([
                                'pemerintahan' => 'Pemerintahan',
                                'pembangunan' => 'Pembangunan',
                                'layanan_publik' => 'Layanan Publik',
                                'sosial_budaya' => 'Sosial Budaya',
                                'ekonomi' => 'Ekonomi',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-folder')
                            ->helperText('Kategori membantu dalam pengelompokan dan pelaporan program')
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label('Status Pelaksanaan')
                            ->placeholder('Pilih status saat ini...')
                            ->options([
                                'perencanaan' => 'Perencanaan',
                                'pelaksanaan' => 'Pelaksanaan',
                                'selesai' => 'Selesai',
                                'ditunda' => 'Ditunda',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-flag')
                            ->helperText('Status terkini dari program kerja')
                            ->live()
                            ->native(false),

                        Forms\Components\Select::make('priority')
                            ->label('Tingkat Prioritas')
                            ->placeholder('Tentukan prioritas program...')
                            ->options([
                                'rendah' => 'Rendah',
                                'sedang' => 'Sedang',
                                'tinggi' => 'Tinggi',
                                'mendesak' => 'Mendesak',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-exclamation-triangle')
                            ->helperText('Prioritas membantu dalam alokasi sumber daya dan penjadwalan')
                            ->native(false),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->placeholder('Pilih tanggal mulai pelaksanaan')
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->helperText('Tanggal dimulainya pelaksanaan program')
                            ->native(false)
                            ->displayFormat('d F Y'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->placeholder('Pilih target tanggal selesai')
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->helperText('Target tanggal penyelesaian program')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->after('start_date'),

                        Forms\Components\Select::make('author_id')
                            ->label('Penanggung Jawab')
                            ->placeholder('Pilih penanggung jawab program...')
                            ->relationship('author', 'name')
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->prefixIcon('heroicon-o-user-circle')
                            ->helperText('Penanggung jawab utama pelaksanaan program')
                            ->searchable(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Program')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->icon('heroicon-o-clipboard-document-check')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->copyable()
                    ->copyMessage('Judul program berhasil disalin!'),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->icon(fn(string $state): string => match ($state) {
                        'pemerintahan' => 'heroicon-o-building-office',
                        'pembangunan' => 'heroicon-o-wrench-screwdriver',
                        'layanan_publik' => 'heroicon-o-users',
                        'sosial_budaya' => 'heroicon-o-heart',
                        'ekonomi' => 'heroicon-o-chart-pie',
                        default => 'heroicon-o-folder',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pemerintahan' => 'info',
                        'pembangunan' => 'warning',
                        'layanan_publik' => 'success',
                        'sosial_budaya' => 'purple',
                        'ekonomi' => 'primary',
                        default => 'gray',
                    })
                    ->tooltip(fn(string $state): string => 'Kategori: ' . ucfirst($state)),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->icon(fn(string $state): string => match ($state) {
                        'perencanaan' => 'heroicon-o-light-bulb',
                        'pelaksanaan' => 'heroicon-o-arrow-path',
                        'selesai' => 'heroicon-o-check-circle',
                        'ditunda' => 'heroicon-o-pause-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'perencanaan' => 'info',
                        'pelaksanaan' => 'warning',
                        'selesai' => 'success',
                        'ditunda' => 'danger',
                        default => 'gray',
                    })
                    ->tooltip(fn(string $state): string => match ($state) {
                        'perencanaan' => 'Masih dalam tahap perencanaan',
                        'pelaksanaan' => 'Sedang dalam pelaksanaan',
                        'selesai' => 'Program telah selesai dilaksanakan',
                        'ditunda' => 'Program ditunda sementara',
                        default => 'Status tidak diketahui',
                    }),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Prioritas')
                    ->searchable()
                    ->sortable()
                    ->icon(fn(string $state): string => match ($state) {
                        'rendah' => 'heroicon-o-arrow-down',
                        'sedang' => 'heroicon-o-minus',
                        'tinggi' => 'heroicon-o-arrow-up',
                        'mendesak' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'rendah' => 'success',
                        'sedang' => 'warning',
                        'tinggi' => 'danger',
                        'mendesak' => 'danger',
                        default => 'gray',
                    })
                    ->tooltip(fn(string $state): string => 'Prioritas: ' . ucfirst($state)),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->placeholder('Belum ditentukan')
                    ->tooltip('Tanggal mulai pelaksanaan'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->color('warning')
                    ->placeholder('Belum ditentukan')
                    ->tooltip('Target tanggal selesai'),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Penanggung Jawab')
                    ->sortable()
                    ->icon('heroicon-o-user-circle')
                    ->color('blue')
                    ->copyable()
                    ->copyMessage('Nama penanggung jawab berhasil disalin!')
                    ->tooltip('Penanggung jawab program'),

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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->tooltip('Tanggal program dibuat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Filter Kategori')
                    ->options([
                        'pemerintahan' => 'Pemerintahan',
                        'pembangunan' => 'Pembangunan',
                        'layanan_publik' => 'Layanan Publik',
                        'sosial_budaya' => 'Sosial Budaya',
                        'ekonomi' => 'Ekonomi',
                    ])
                    ->multiple()
                    ->placeholder('Semua kategori'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'perencanaan' => 'Perencanaan',
                        'pelaksanaan' => 'Pelaksanaan',
                        'selesai' => 'Selesai',
                        'ditunda' => 'Ditunda',
                    ])
                    ->multiple()
                    ->placeholder('Semua status'),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Filter Prioritas')
                    ->options([
                        'rendah' => 'Rendah',
                        'sedang' => 'Sedang',
                        'tinggi' => 'Tinggi',
                        'mendesak' => 'Mendesak',
                    ])
                    ->multiple()
                    ->placeholder('Semua prioritas'),

                Tables\Filters\SelectFilter::make('author')
                    ->label('Filter Penanggung Jawab')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua penanggung jawab'),

                Tables\Filters\Filter::make('active_programs')
                    ->label('Program Aktif')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereIn('status', ['perencanaan', 'pelaksanaan'])
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('urgent_programs')
                    ->label('Program Mendesak')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('priority', 'mendesak')
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('this_month')
                    ->label('Program Bulan Ini')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereBetween('start_date', [now()->startOfMonth(), now()->endOfMonth()])
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail program'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit program kerja'),

                Tables\Actions\Action::make('mark_completed')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn($record) => $record->update(['status' => 'selesai']))
                    ->visible(fn($record) => $record->status !== 'selesai')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Program Selesai')
                    ->modalDescription('Apakah Anda yakin program ini telah selesai dilaksanakan?')
                    ->tooltip('Tandai program sebagai selesai'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus program')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Program Kerja')
                    ->modalDescription('Apakah Anda yakin ingin menghapus program kerja ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Program Terpilih')
                        ->modalDescription('Semua program yang dipilih akan dihapus permanen.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),

                    Tables\Actions\BulkAction::make('update_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-flag')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options([
                                    'perencanaan' => 'Perencanaan',
                                    'pelaksanaan' => 'Pelaksanaan',
                                    'selesai' => 'Selesai',
                                    'ditunda' => 'Ditunda',
                                ])
                                ->required(),
                        ])
                        ->action(fn($records, $data) => $records->each->update(['status' => $data['status']]))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->tooltip('Ubah status program terpilih'),

                    Tables\Actions\BulkAction::make('export_programs')
                        ->label('Export Program')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Add export logic here
                        })
                        ->deselectRecordsAfterCompletion()
                        ->tooltip('Export data program terpilih'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Program Kerja')
            ->emptyStateDescription('Mulai dengan membuat program kerja pertama untuk organisasi Anda.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Program Pertama')
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
            'index' => Pages\ListWorkPrograms::route('/'),
            'create' => Pages\CreateWorkProgram::route('/create'),
            'edit' => Pages\EditWorkProgram::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
