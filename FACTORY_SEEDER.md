# Dokumentasi Factory dan Seeder

## Daftar Factory

### 1. UserFactory
Factory untuk model User dengan field:
- name: Nama pengguna
- email: Alamat email (unik)
- email_verified_at: Tanggal verifikasi email
- password: Password (default: 'password')
- remember_token: Token untuk remember me
- role: Role pengguna (super_admin, editor, penulis, kontributor)
- phone: Nomor telepon
- avatar: Avatar pengguna
- bio: Biografi singkat
- position: Jabatan/posisi

Method tambahan:
- superAdmin(): Membuat user dengan role super_admin
- editor(): Membuat user dengan role editor
- penulis(): Membuat user dengan role penulis
- kontributor(): Membuat user dengan role kontributor

### 2. NewsFactory
Factory untuk model News dengan field:
- title: Judul berita
- slug: Slug untuk URL
- content: Konten berita
- excerpt: Ringkasan berita
- featured_image: Gambar utama
- status: Status berita (draft, published, archived)
- author_id: ID penulis
- published_at: Tanggal publikasi

Method tambahan:
- published(): Membuat berita dengan status published
- draft(): Membuat berita dengan status draft
- archived(): Membuat berita dengan status archived

### 3. CategoryFactory
Factory untuk model Category dengan field:
- name: Nama kategori
- slug: Slug untuk URL
- description: Deskripsi kategori

### 4. TagFactory
Factory untuk model Tag dengan field:
- name: Nama tag
- slug: Slug untuk URL

### 5. GalleryFactory
Factory untuk model Gallery dengan field:
- title: Judul galeri
- description: Deskripsi galeri
- news_id: ID berita terkait

### 6. GalleryImageFactory
Factory untuk model GalleryImage dengan field:
- gallery_id: ID galeri
- image_path: Path gambar
- caption: Keterangan gambar
- sort_order: Urutan tampilan

### 7. ComplaintFactory
Factory untuk model Complaint dengan field:
- name: Nama pelapor
- email: Email pelapor
- phone: Nomor telepon pelapor
- category: Kategori aduan
- description: Deskripsi aduan
- status: Status aduan (baru, diproses, selesai, ditolak)
- document: Dokumen pendukung
- photo: Foto pendukung
- response: Tanggapan terhadap aduan

Method tambahan:
- baru(): Membuat aduan dengan status baru
- diproses(): Membuat aduan dengan status diproses
- selesai(): Membuat aduan dengan status selesai
- ditolak(): Membuat aduan dengan status ditolak

### 8. SocialMediaFactory
Factory untuk model SocialMedia dengan field:
- platform: Platform media sosial
- url: URL konten
- embed_code: Kode embed
- title: Judul konten
- description: Deskripsi konten
- thumbnail: Gambar thumbnail

### 9. HeroFactory
Factory untuk model Hero dengan field:
- title: Judul hero
- description: Deskripsi hero
- image: Gambar hero
- link: Tautan hero
- is_active: Status aktif
- sort_order: Urutan tampilan

Method tambahan:
- active(): Membuat hero dengan status aktif
- inactive(): Membuat hero dengan status tidak aktif

### 10. BannerFactory
Factory untuk model Banner dengan field:
- title: Judul banner
- image: Gambar banner
- link: Tautan banner
- position: Posisi banner (top, sidebar, bottom)
- size: Ukuran banner
- is_active: Status aktif
- start_date: Tanggal mulai tampil
- end_date: Tanggal akhir tampil
- click_count: Jumlah klik

Method tambahan:
- active(): Membuat banner dengan status aktif
- inactive(): Membuat banner dengan status tidak aktif

### 11. WorkProgramFactory
Factory untuk model WorkProgram dengan field:
- title: Judul program kerja
- description: Deskripsi program kerja
- year: Tahun program
- budget: Anggaran program
- target: Target program
- achievement: Pencapaian program
- status: Status program (planning, ongoing, completed, delayed)

Method tambahan:
- planning(): Membuat program dengan status planning
- ongoing(): Membuat program dengan status ongoing
- completed(): Membuat program dengan status completed
- delayed(): Membuat program dengan status delayed

## Daftar Seeder

### 1. UserSeeder
Seeder untuk model User yang membuat:
- 1 super admin dengan data khusus
- 1 editor dengan data khusus
- 1 penulis dengan data khusus
- 1 kontributor dengan data khusus
- 10 user acak menggunakan factory

### 2. CategorySeeder
Seeder untuk model Category yang membuat 8 kategori berita utama:
- Berita Umum
- Pemerintahan
- Ekonomi
- Pendidikan
- Kesehatan
- Olahraga
- Wisata
- Budaya

### 3. TagSeeder
Seeder untuk model Tag yang membuat 10 tag umum:
- penting
- terkini
- lokal
- nasional
- internasional
- opini
- analisis
- wawancara
- liputan
- khusus

### 4. NewsSeeder
Seeder untuk model News yang membuat:
- Memanggil CategorySeeder, TagSeeder, dan UserSeeder terlebih dahulu
- 50 berita published menggunakan factory
- Menambahkan 1-3 kategori acak ke setiap berita
- Menambahkan 1-5 tag acak ke setiap berita
- Menentukan author acak untuk setiap berita

### 5. GallerySeeder
Seeder untuk model Gallery yang membuat:
- Memanggil NewsSeeder terlebih dahulu
- 20 galeri menggunakan factory
- Menambahkan 3-8 gambar ke setiap galeri menggunakan GalleryImageFactory

### 6. ComplaintSeeder
Seeder untuk model Complaint yang membuat:
- 10 aduan dengan status baru
- 8 aduan dengan status diproses
- 7 aduan dengan status selesai
- 5 aduan dengan status ditolak

### 7. SocialMediaSeeder
Seeder untuk model SocialMedia yang membuat:
- 15 konten media sosial menggunakan factory

### 8. HeroSeeder
Seeder untuk model Hero yang membuat 5 hero carousel dengan data khusus:
- Selamat Datang di Warta Daerah Muna Barat
- Program Kerja Unggulan
- Galeri Foto Kegiatan
- Layanan Publik
- Aduan Masyarakat

### 9. BannerSeeder
Seeder untuk model Banner yang membuat:
- 5 banner dengan data khusus untuk berbagai posisi
- 5 banner tambahan menggunakan factory

### 10. WorkProgramSeeder
Seeder untuk model WorkProgram yang membuat:
- 5 program kerja dengan data khusus
- 15 program kerja tambahan menggunakan factory

### 11. DatabaseSeeder
Seeder utama yang memanggil semua seeder lainnya dalam urutan yang tepat.

## Cara Menggunakan

### Menjalankan Semua Seeder
```bash
php artisan db:seed
```

### Menjalankan Seeder Tertentu
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=NewsSeeder
# dll.
```

### Menjalankan Factory di Tinker
```bash
php artisan tinker
```

```php
// Membuat 5 user
User::factory(5)->create();

// Membuat 10 berita published
News::factory(10)->published()->create();

// Membuat 1 user super admin
User::factory()->superAdmin()->create([
    'name' => 'Nama Admin',
    'email' => 'admin@example.com'
]);
```