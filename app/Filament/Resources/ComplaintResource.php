<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
use App\Filament\Resources\ComplaintResource\RelationManagers;
use App\Models\Complaint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ComplaintResource extends Resource
{

    protected static ?string $model = Complaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Aduan Masyarakat';

    protected static ?string $modelLabel = 'Aduan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('👤 Data Pelapor')
                    ->description('Informasi identitas pelapor untuk keperluan tindak lanjut dan komunikasi.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Ahmad Susanto')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user')
                            ->helperText('Nama lengkap sesuai identitas resmi'),

                        Forms\Components\TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('contoh@email.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-at-symbol')
                            ->helperText('Email yang aktif untuk pemberitahuan status aduan'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->placeholder('08xxxxxxxxxx')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-phone')
                            ->helperText('Nomor yang dapat dihubungi (WhatsApp/SMS)')
                            ->mask('9999-9999-9999'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('📝 Detail Aduan')
                    ->description('Uraian lengkap mengenai keluhan atau aduan yang disampaikan.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('category')
                            ->label('Kategori Aduan')
                            ->placeholder('Contoh: Pelayanan Publik, Infrastruktur, Lingkungan')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-tag')
                            ->helperText('Jenis atau bidang yang terkait dengan aduan'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Aduan')
                            ->placeholder('Jelaskan secara detail kronologi kejadian, lokasi, dan dampak yang dialami...')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Uraikan dengan jelas dan lengkap agar memudahkan penanganan'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('⚙️ Pengelolaan Aduan')
                    ->description('Status dan progress penanganan aduan (dikelola oleh administrator).')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Penanganan')
                            ->required()
                            ->options([
                                'baru' => 'Baru',
                                'diproses' => 'Diproses',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                            ])
                            ->default('baru')
                            ->prefixIcon('heroicon-o-flag')
                            ->helperText('Status saat ini dari penanganan aduan')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Optional: Add logic here if needed
                            }),

                        Forms\Components\Textarea::make('response')
                            ->label('Respons/Tanggapan')
                            ->placeholder('Berikan tanggapan atau penjelasan terkait penanganan aduan ini...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Tanggapan resmi dari pihak terkait untuk pelapor')
                            ->visible(fn(callable $get) => in_array($get('status'), ['diproses', 'selesai', 'ditolak'])),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('📎 Lampiran Pendukung')
                    ->description('Upload dokumen atau foto yang mendukung aduan Anda (opsional).')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        Forms\Components\FileUpload::make('document')
                            ->label('Dokumen Pendukung')
                            ->directory('complaints/documents')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(2048) // 2MB
                            ->helperText('Format: PDF, DOC, DOCX. Maksimal 2MB')
                            ->downloadable()
                            ->openable(),

                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto/Gambar')
                            ->directory('complaints/photos')
                            ->image()
                            ->maxSize(2048) // 2MB
                            ->helperText('Format: JPG, PNG, GIF. Maksimal 2MB')
                            ->imageEditor()
                            ->downloadable()
                            ->openable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pelapor')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-o-user')
                    ->copyable()
                    ->copyMessage('Nama berhasil disalin!')
                    ->tooltip('Klik untuk menyalin nama'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-at-symbol')
                    ->color('blue')
                    ->copyable()
                    ->copyMessage('Email berhasil disalin!')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->color('green')
                    ->copyable()
                    ->copyMessage('Nomor telepon berhasil disalin!')
                    ->tooltip('Klik untuk menyalin nomor telepon'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-tag')
                    ->badge()
                    ->color('info')
                    ->tooltip('Jenis aduan'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->icon(fn(string $state): string => match ($state) {
                        'baru' => 'heroicon-o-clock',
                        'diproses' => 'heroicon-o-arrow-path',
                        'selesai' => 'heroicon-o-check-circle',
                        'ditolak' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'baru' => 'primary',
                        'diproses' => 'warning',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->tooltip(fn(string $state): string => match ($state) {
                        'baru' => 'Aduan baru masuk, belum ditangani',
                        'diproses' => 'Sedang dalam proses penanganan',
                        'selesai' => 'Aduan telah selesai ditangani',
                        'ditolak' => 'Aduan ditolak dengan alasan tertentu',
                        default => 'Status tidak diketahui',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('response')
                    ->label('Respons')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Belum ada respons'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Aduan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('gray')
                    ->tooltip('Waktu aduan masuk')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'baru' => 'Baru',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ])
                    ->multiple()
                    ->placeholder('Pilih status'),

                Tables\Filters\Filter::make('recent')
                    ->label('Aduan Terbaru (7 hari)')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),

                Tables\Filters\Filter::make('urgent')
                    ->label('Perlu Tindak Lanjut')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'baru'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail aduan'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit status atau data aduan'),

                Tables\Actions\Action::make('mark_processed')
                    ->label('Proses')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(fn($record) => $record->update(['status' => 'diproses']))
                    ->visible(fn($record) => $record->status === 'baru')
                    ->requiresConfirmation()
                    ->tooltip('Tandai sedang diproses'),

                Tables\Actions\Action::make('mark_completed')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn($record) => $record->update(['status' => 'selesai']))
                    ->visible(fn($record) => $record->status === 'diproses')
                    ->requiresConfirmation()
                    ->tooltip('Tandai telah selesai'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Aduan Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua aduan yang dipilih? Data akan hilang permanen.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),

                    Tables\Actions\BulkAction::make('mark_all_processed')
                        ->label('Tandai Semua Diproses')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(fn($records) => $records->each->update(['status' => 'diproses']))
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Aduan')
            ->emptyStateDescription('Saat ini belum ada aduan masyarakat yang masuk ke sistem.')
            ->emptyStateIcon('heroicon-o-inbox-stack');
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
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'view' => Pages\ViewComplaint::route('/{record}'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
