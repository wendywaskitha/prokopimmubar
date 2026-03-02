# Role-Based Access Control (RBAC) dengan Filament Shield

## Instalasi dan Konfigurasi

1. Menginstal paket Filament Shield:
   ```bash
   composer require bezhansalleh/filament-shield
   ```

2. Mempublikasikan konfigurasi:
   ```bash
   php artisan vendor:publish --tag=filament-shield-config
   ```

3. Mempublikasikan dan menjalankan migrasi:
   ```bash
   php artisan vendor:publish --tag=permission-migrations
   php artisan migrate
   ```

4. Menghasilkan permissions dan policies untuk semua entitas:
   ```bash
   php artisan shield:generate --all --panel=admin
   ```

## Konfigurasi Model User

Menambahkan trait `HasRoles` dari Spatie Permission ke model `User`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    // ...
}
```

## Pembuatan Super Admin

Membuat user super admin dengan perintah:
```bash
php artisan shield:super-admin --user=1
```

## Implementasi RBAC ke NewsResource

Menambahkan trait `HasPageShield` ke:
1. `NewsResource`
2. `ListNews`
3. `CreateNews`
4. `EditNews`

## Pembatasan Akses Berdasarkan Role

### NewsPolicy
Mengimplementasikan pembatasan akses di `NewsPolicy`:
- Semua role bisa melihat daftar berita
- Hanya editor, penulis, dan super admin yang bisa membuat berita
- Penulis hanya bisa mengupdate dan menghapus berita mereka sendiri
- Bulk actions hanya tersedia untuk super admin dan editor

### NewsResource
Mengimplementasikan pembatasan akses di `NewsResource`:
- Menambahkan method `canViewAny()` dan `canCreate()` untuk kontrol akses
- Memodifikasi query untuk membatasi data yang ditampilkan berdasarkan role
- Menghapus bulk actions untuk role yang tidak berhak
- Menambahkan indikator visual untuk berita yang dibuat oleh user sendiri

### CreateNews
Mengimplementasikan pembatasan akses di `CreateNews`:
- Menetapkan `author_id` secara otomatis ke user yang sedang login
- Mengatur status ke draft secara otomatis untuk kontributor

### EditNews
Mengimplementasikan pembatasan akses di `EditNews`:
- Mengontrol akses berdasarkan role dan kepemilikan berita
- Mengatur aksi delete hanya untuk user yang berhak
- Menambahkan pengecekan otorisasi akses ke halaman edit

## Pengujian Fungsionalitas RBAC

Fungsionalitas RBAC telah diuji dengan berbagai role pengguna:
- Super Admin: Akses penuh ke semua fitur
- Editor: Akses penuh ke manajemen berita
- Penulis: Akses terbatas hanya ke berita mereka sendiri
- Kontributor: Akses sangat terbatas, hanya bisa membuat draft

## Dokumentasi Implementasi

Implementasi RBAC dengan Filament Shield telah selesai dan berfungsi sesuai dengan kebutuhan sistem. Pembatasan akses telah diterapkan pada NewsResource dengan mempertimbangkan kebutuhan masing-masing role pengguna.