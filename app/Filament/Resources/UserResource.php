<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleBasedResource;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('👤 Informasi Dasar')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->placeholder('Contoh: Ahmad Susanto')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-user')
                                    ->helperText('Nama lengkap pengguna sesuai identitas resmi')
                                    ->live(onBlur: true),

                                Forms\Components\TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->placeholder('contoh@email.com')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-at-symbol')
                                    ->helperText('Email akan digunakan untuk login dan komunikasi sistem')
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('password')
                                    ->label('Kata Sandi')
                                    ->placeholder('Minimal 8 karakter')
                                    ->password()
                                    ->required()
                                    ->maxLength(255)
                                    ->visible(fn($livewire) => $livewire instanceof Pages\CreateUser)
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                    ->prefixIcon('heroicon-o-lock-closed')
                                    ->helperText('Gunakan kombinasi huruf, angka, dan simbol untuk keamanan')
                                    ->revealable()
                                    ->rule('min:8'),

                                Forms\Components\Select::make('role')
                                    ->label('Peran Pengguna')
                                    ->placeholder('Pilih peran...')
                                    ->options(function () {
                                        $user = Auth::user();
                                        // Only super admin can assign any role
                                        if ($user && $user->isSuperAdmin()) {
                                            return User::ROLES;
                                        }
                                        // Editor can only assign roles below editor
                                        elseif ($user && $user->isEditor()) {
                                            return [
                                                'penulis' => User::ROLES['penulis'],
                                                'kontributor' => User::ROLES['kontributor'],
                                            ];
                                        }
                                        // Others cannot change roles
                                        else {
                                            return [];
                                        }
                                    })
                                    ->required()
                                    ->prefixIcon('heroicon-o-shield-check')
                                    ->helperText('Peran menentukan hak akses dan fitur yang dapat digunakan')
                                    ->live()
                                    ->native(false)
                                    ->disabled(function () {
                                        $user = Auth::user();
                                        // Only super admin and editor can change roles
                                        return !$user || (!$user->isSuperAdmin() && !$user->isEditor());
                                    })
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('📋 Profil')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\TextInput::make('phone')
                                    ->label('Nomor Telepon')
                                    ->placeholder('08xxxxxxxxxx')
                                    ->tel()
                                    ->maxLength(20)
                                    ->prefixIcon('heroicon-o-phone')
                                    ->helperText('Nomor telepon aktif untuk keperluan komunikasi')
                                    ->mask('9999-9999-9999'),

                                Forms\Components\TextInput::make('position')
                                    ->label('Jabatan/Posisi')
                                    ->placeholder('Contoh: Manager IT, Staff Admin')
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-o-briefcase')
                                    ->helperText('Jabatan atau posisi pengguna dalam organisasi'),

                                Forms\Components\FileUpload::make('avatar')
                                    ->label('📸 Foto Profil')
                                    ->image()
                                    ->directory('avatars')
                                    ->avatar()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(1024) // 1MB
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['1:1'])
                                    ->helperText('Upload foto profil persegi. Format: JPG, PNG, WebP. Maksimal 1MB.')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('bio')
                                    ->label('Biografi/Tentang')
                                    ->placeholder('Ceritakan singkat tentang background, pengalaman, atau keahlian...')
                                    ->rows(4)
                                    ->maxLength(500)
                                    ->helperText('Deskripsi singkat tentang pengguna (maksimal 500 karakter)')
                                    ->columnSpanFull()
                                    ->live(onBlur: true),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();

        $table = $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=0D8ABC&color=fff')
                    ->tooltip('Foto profil pengguna'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-user')
                    ->copyable()
                    ->copyMessage('Nama berhasil disalin!')
                    ->tooltip('Klik untuk menyalin nama'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-at-symbol')
                    ->color('blue')
                    ->copyable()
                    ->copyMessage('Email berhasil disalin!')
                    ->tooltip('Klik untuk menyalin email'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-phone')
                    ->color('green')
                    ->copyable()
                    ->copyMessage('Nomor telepon berhasil disalin!')
                    ->placeholder('Tidak ada nomor')
                    ->tooltip('Klik untuk menyalin nomor telepon')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Peran')
                    ->searchable(false) // Disable direct search on this relationship column
                    ->sortable(false) // Disable direct sorting on this relationship column
                    ->formatStateUsing(fn($state, $record) => $record->roles->first() ?
                        (User::ROLES[$record->roles->first()->name] ?? ucfirst($record->roles->first()->name)) :
                        'Tidak ada peran')
                    ->icon(fn($state, $record) => match ($record->roles->first()?->name) {
                        'super_admin' => 'heroicon-o-shield-check',
                        'editor' => 'heroicon-o-pencil-square',
                        'penulis' => 'heroicon-o-user-circle',
                        'kontributor' => 'heroicon-o-user',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn($state, $record) => match ($record->roles->first()?->name) {
                        'super_admin' => 'danger',
                        'editor' => 'warning',
                        'penulis' => 'success',
                        'kontributor' => 'info',
                        default => 'gray',
                    })
                    ->tooltip(fn($state, $record) => 'Peran: ' . ($record->roles->first() ?
                        (User::ROLES[$record->roles->first()->name] ?? ucfirst($record->roles->first()->name)) :
                        'Tidak ada peran')),

                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-briefcase')
                    ->color('purple')
                    ->placeholder('Tidak ada jabatan')
                    ->tooltip('Jabatan dalam organisasi')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('bio')
                    ->label('Biografi')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->placeholder('Tidak ada biografi')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->tooltip('Tanggal pendaftaran')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->tooltip('Terakhir kali profil diperbarui')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Peran')
                    ->relationship('roles', 'name')
                    ->options(User::ROLES)
                    ->multiple()
                    ->placeholder('Semua peran'),

                Tables\Filters\Filter::make('has_avatar')
                    ->label('Memiliki Foto Profil')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('avatar'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_phone')
                    ->label('Memiliki Nomor Telepon')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('phone'))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Pengguna Baru (7 hari)')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7)))
                    ->toggle(),
            ])
            ->defaultSort(function (Builder $query) {
                // Sort super_admin users to the top, then by created_at descending
                return $query->orderByRaw("CASE WHEN EXISTS (SELECT 1 FROM model_has_roles mhr JOIN roles r ON mhr.role_id = r.id WHERE mhr.model_id = users.id AND r.name = 'super_admin') THEN 0 ELSE 1 END")
                             ->orderBy('created_at', 'desc');
            });

        // Define actions based on user role
        $actions = [
            // Add impersonate action
            Impersonate::make()
                ->icon('heroicon-o-user-circle')
                ->tooltip('Masuk sebagai pengguna ini')
                ->visible(fn($record) => Auth::user()->id !== $record->id && !$record->hasRole('super_admin'))
                ->redirectTo('/admin'),
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail pengguna'),

                // Only super admin and editor can edit users
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit pengguna')
                    ->visible(fn() => $user && ($user->isSuperAdmin() || $user->isEditor())),

                // Only super admin can reset passwords and delete users
                Tables\Actions\Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->revealable(),
                    ])
                    ->action(function ($record, $data) {
                        $record->update(['password' => Hash::make($data['new_password'])]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password Pengguna')
                    ->modalDescription('Masukkan password baru untuk pengguna ini.')
                    ->tooltip('Reset password pengguna')
                    ->visible(fn() => $user && $user->isSuperAdmin()),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus pengguna')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Pengguna')
                    ->modalDescription('Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait akan ikut terhapus.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->visible(fn() => $user && $user->isSuperAdmin()),
            ])
                ->iconButton()
                ->tooltip('Aksi lainnya')
        ];

        $table->actions($actions);

        // Define bulk actions based on user role
        $bulkActions = [
            Tables\Actions\BulkActionGroup::make(
                // Only super admin can perform bulk actions
                ($user && $user->isSuperAdmin()) ? [
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pengguna Terpilih')
                        ->modalDescription('Semua pengguna yang dipilih akan dihapus permanen beserta data terkaitnya.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),

                    Tables\Actions\BulkAction::make('change_role')
                        ->label('Ubah Peran')
                        ->icon('heroicon-o-shield-check')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('role')
                                ->label('Peran Baru')
                                ->options(User::ROLES)
                                ->required(),
                        ])
                        ->action(function ($records, $data) {
                            foreach ($records as $record) {
                                $record->syncRoles([$data['role']]);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation()
                        ->tooltip('Ubah peran pengguna terpilih'),

                    Tables\Actions\BulkAction::make('export_users')
                        ->label('Export Data')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            // Add export logic here
                        })
                        ->deselectRecordsAfterCompletion()
                        ->tooltip('Export data pengguna terpilih'),
                ] : []
            ),
        ];

        $table->bulkActions($bulkActions);

        return $table
            ->emptyStateHeading('Belum Ada Pengguna')
            ->emptyStateDescription('Mulai dengan menambahkan pengguna pertama ke sistem.')
            ->emptyStateIcon('heroicon-o-user-plus')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Pengguna Pertama')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
