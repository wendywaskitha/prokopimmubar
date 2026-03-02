<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaResource\Pages;
use App\Filament\Resources\AgendaResource\RelationManagers;
use App\Models\Agenda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Manajemen Konten';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Agenda')
                    ->description('Masukkan detail informasi agenda')
                    ->icon('heroicon-m-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Judul Agenda')
                                    ->placeholder('Masukkan judul agenda...')
                                    ->prefixIcon('heroicon-m-document-text')
                                    ->helperText('Judul yang menarik dan deskriptif')
                                    ->columnSpan(2),

                                Textarea::make('description')
                                    ->maxLength(65535)
                                    ->label('Deskripsi')
                                    ->placeholder('Masukkan deskripsi agenda...')
                                    ->rows(4)
                                    ->columnSpan(2)
                                    ->helperText('Jelaskan detail agenda secara lengkap'),

                                TextInput::make('location')
                                    ->maxLength(255)
                                    ->label('Lokasi')
                                    ->placeholder('Masukkan lokasi agenda...')
                                    ->prefixIcon('heroicon-m-map-pin')
                                    ->helperText('Lokasi pelaksanaan agenda')
                                    ->columnSpan(1),

                                Toggle::make('is_published')
                                    ->required()
                                    ->default(true)
                                    ->label('Publikasikan')
                                    ->helperText('Aktifkan untuk mempublikasikan agenda')
                                    ->inline(false)
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Waktu Pelaksanaan')
                    ->description('Tentukan waktu mulai dan selesai agenda')
                    ->icon('heroicon-m-clock')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->required()
                                    ->label('Tanggal & Waktu Mulai')
                                    ->helperText('Pilih tanggal dan waktu mulai agenda')
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->prefixIcon('heroicon-m-play')
                                    ->minDate(now())
                                    ->columnSpan(1),

                                DateTimePicker::make('end_date')
                                    ->required()
                                    ->label('Tanggal & Waktu Selesai')
                                    ->helperText('Pilih tanggal dan waktu selesai agenda')
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->prefixIcon('heroicon-m-stop')
                                    ->minDate(fn ($get) => $get('start_date'))
                                    ->columnSpan(1),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Judul Agenda')
                    ->description(fn (Agenda $record): string => $record->location ? "📍 {$record->location}" : 'Tidak ada lokasi')
                    ->weight(FontWeight::Medium)
                    ->wrap(),

                TextColumn::make('start_date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Mulai')
                    ->icon('heroicon-m-play')
                    ->color(fn (Agenda $record): string => $record->start_date->isPast() ? 'danger' : 'success')
                    ->weight(FontWeight::Medium),

                TextColumn::make('end_date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Selesai')
                    ->icon('heroicon-m-stop')
                    ->color(fn (Agenda $record): string => $record->end_date->isPast() ? 'gray' : 'warning'),

                BadgeColumn::make('status')
                    ->label('Status Waktu')
                    ->getStateUsing(function (Agenda $record): string {
                        $now = now();
                        if ($now->isBefore($record->start_date)) {
                            return 'Akan Datang';
                        } elseif ($now->isBetween($record->start_date, $record->end_date)) {
                            return 'Berlangsung';
                        } else {
                            return 'Selesai';
                        }
                    })
                    ->colors([
                        'primary' => 'Akan Datang',
                        'success' => 'Berlangsung',
                        'gray' => 'Selesai',
                    ])
                    ->icons([
                        'heroicon-m-clock' => 'Akan Datang',
                        'heroicon-m-play-circle' => 'Berlangsung',
                        'heroicon-m-check-circle' => 'Selesai',
                    ]),

                IconColumn::make('is_published')
                    ->boolean()
                    ->label('Publikasi')
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (Agenda $record): string => $record->is_published ? 'Sudah dipublikasikan' : 'Belum dipublikasikan'),

                TextColumn::make('creator.name')
                    ->label('Dibuat oleh')
                    ->icon('heroicon-m-user')
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Dibuat')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->placeholder('Semua Agenda')
                    ->trueLabel('Sudah Dipublikasikan')
                    ->falseLabel('Belum Dipublikasikan')
                    ->native(false),

                SelectFilter::make('status_waktu')
                    ->label('Status Waktu')
                    ->options([
                        'akan_datang' => 'Akan Datang',
                        'berlangsung' => 'Berlangsung',
                        'selesai' => 'Selesai',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $value): Builder {
                                $now = now();
                                return match ($value) {
                                    'akan_datang' => $query->where('start_date', '>', $now),
                                    'berlangsung' => $query->where('start_date', '<=', $now)
                                                          ->where('end_date', '>=', $now),
                                    'selesai' => $query->where('end_date', '<', $now),
                                    default => $query,
                                };
                            }
                        );
                    })
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-m-eye'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->icon('heroicon-m-pencil-square'),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-m-trash'),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-m-trash'),
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publikasikan')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['is_published' => true]));
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Publikasikan Agenda')
                        ->modalDescription('Apakah Anda yakin ingin mempublikasikan agenda yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Publikasikan'),
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Batalkan Publikasi')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(fn ($record) => $record->update(['is_published' => false]));
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Publikasi Agenda')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan publikasi agenda yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Batalkan'),
                ])
                ->label('Aksi Massal'),
            ])
            ->defaultSort('start_date', 'asc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Agenda';
    }

    public static function getModelLabel(): string
    {
        return 'Agenda';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Agenda';
    }

    public static function getNavigationBadge(): ?string
    {
        $today = today();
        $count = static::getModel()::where('is_published', true)
            ->whereBetween('start_date', [$today, $today->copy()->endOfDay()])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }
}
