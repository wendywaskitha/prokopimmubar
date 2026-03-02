# Dokumentasi Proyek Website Warta Daerah Kabupaten Muna Barat

## Deskripsi Proyek
Website Warta Daerah Kabupaten Muna Barat adalah portal berita yang dikelola oleh administrator (bukan OPD) untuk mempublikasikan program kerja kepala daerah, berita daerah, serta galeri media sosial seperti YouTube dan TikTok. Proyek ini telah mencakup pengembangan backend lengkap dengan admin panel menggunakan FilamentPHP, serta frontend yang telah dikembangkan menggunakan Vue.js.

## Teknologi yang Digunakan
- Backend Admin Panel: FilamentPHP versi 3.3.36
- Frontend: Vue.js
- Database: MySQL (bawaan Laravel)

## Struktur Role dan Permission
1. **Super Admin**: Akses penuh ke seluruh sistem
2. **Editor**: Mengelola berita, program kerja, dan galeri media sosial
3. **Penulis**: Membuat dan mengedit berita yang ditulisnya sendiri
4. **Kontributor**: Mengirim berita untuk direview

## Fitur Utama
1. Manajemen Berita (CRUD dengan kategori dan tag)
2. Galeri Media Sosial (YouTube, TikTok, dll)
3. Publikasi Program Kerja Kepala Daerah
4. Manajemen Pengguna berdasarkan Role

## Fitur Tambahan yang Akan Dikembangkan
1. **Manajemen User**: Mengelola pengguna sistem dengan berbagai role
2. **Manajemen Hero Carousel**: Mengelola konten hero carousel untuk halaman depan
3. **Manajemen Banner**: Mengelola banner iklan atau informasi di halaman depan
4. **Fitur Aduan Masyarakat**: Sistem untuk menerima dan menangani aduan dari masyarakat
5. **Manajemen Galeri Berita**: Membuat dan mengelola berita dalam format galeri dengan multiple image
6. **Pembatasan Menu Berdasarkan Role**: Mengimplementasikan panel dan pembatasan menu pada role editor, penulis, dan kontributor

## Task untuk Pengembangan Fitur

### 1. Manajemen User
- [x] Membuat model User dengan field tambahan untuk informasi profil
- [x] Mengembangkan resource Filament untuk manajemen user
- [x] Menambahkan fitur reset password
- [x] Mengimplementasikan verifikasi email
- [x] Membuat halaman profil user

### 2. Manajemen Hero Carousel
- [x] Membuat model Hero dengan field judul, deskripsi, gambar, dan link
- [x] Mengembangkan resource Filament untuk manajemen hero carousel
- [x] Menambahkan fitur pengaturan urutan tampilan
- [x] Mengimplementasikan status aktif/tidak aktif
- [x] Membuat preview carousel di admin panel

### 3. Manajemen Banner
- [x] Membuat model Banner dengan field judul, gambar, link, dan posisi
- [x] Mengembangkan resource Filament untuk manajemen banner
- [x] Menambahkan fitur penjadwalan tampilan banner
- [x] Mengimplementasikan berbagai ukuran dan posisi banner
- [x] Membuat statistik klik banner

### 4. Fitur Aduan Masyarakat
- [x] Membuat model Complaint dengan field nama, email, telepon, kategori, dan deskripsi
- [x] Mengembangkan resource Filament untuk manajemen aduan
- [x] Menambahkan fitur status tracking (baru, diproses, selesai, ditolak)
- [x] Mengimplementasikan notifikasi email kepada pelapor
- [x] Membuat form pengaduan untuk frontend

### 5. Manajemen Galeri Berita
- [x] Membuat model Gallery dengan relasi ke berita
- [x] Mengembangkan resource Filament untuk manajemen galeri
- [x] Menambahkan fitur upload multiple image
- [x] Mengimplementasikan caption untuk setiap gambar
- [x] Membuat tampilan galeri di frontend

## Task untuk Pengembangan API Backend

### 1. API untuk Fitur Aduan Masyarakat
- [x] Membuat endpoint API untuk submit aduan masyarakat
- [x] Membuat endpoint API untuk mendapatkan daftar aduan
- [x] Membuat endpoint API untuk mendapatkan detail aduan
- [x] Mengimplementasikan validasi data untuk API aduan
- [x] Mengimplementasikan rate limiting untuk API aduan

### 2. API untuk Fitur Galeri Berita
- [x] Membuat endpoint API untuk mendapatkan daftar galeri
- [x] Membuat endpoint API untuk mendapatkan detail galeri
- [x] Mengoptimalkan response API dengan pagination
- [x] Menambahkan filter dan search untuk API galeri

### 3. API untuk Fitur Berita
- [x] Membuat endpoint API untuk mendapatkan daftar berita
- [x] Membuat endpoint API untuk mendapatkan detail berita
- [x] Membuat endpoint API untuk mendapatkan berita berdasarkan kategori
- [x] Membuat endpoint API untuk mendapatkan berita populer
- [x] Mengimplementasikan pagination untuk API berita

### 4. API untuk Fitur Media Sosial
- [x] Membuat endpoint API untuk mendapatkan daftar media sosial
- [x] Membuat endpoint API untuk mendapatkan media sosial berdasarkan platform
- [x] Mengimplementasikan caching untuk API media sosial

## Factory dan Seeder

Factory dan Seeder telah dibuat untuk semua model dalam sistem. Dokumentasi lengkap tersedia di file FACTORY_SEEDER.md.

### Factory yang Tersedia:
- UserFactory
- NewsFactory
- CategoryFactory
- TagFactory
- GalleryFactory
- GalleryImageFactory
- ComplaintFactory
- SocialMediaFactory
- HeroFactory
- BannerFactory
- WorkProgramFactory

### Seeder yang Tersedia:
- UserSeeder
- CategorySeeder
- TagSeeder
- NewsSeeder
- GallerySeeder
- ComplaintSeeder
- SocialMediaSeeder
- HeroSeeder
- BannerSeeder
- WorkProgramSeeder
- DatabaseSeeder (seeder utama yang memanggil semua seeder lainnya)

## API untuk Frontend Vue.js

### Endpoint API yang Tersedia

1. **Galeri Berita**
   - `GET /api/v1/galleries` - Mendapatkan semua galeri berita
   - `GET /api/v1/galleries/{id}` - Mendapatkan detail galeri berita tertentu

2. **Aduan Masyarakat**
   - `POST /api/v1/complaints` - Submit aduan masyarakat
   - `GET /api/v1/complaints` - Mendapatkan daftar aduan (dengan autentikasi)
   - `GET /api/v1/complaints/{id}` - Mendapatkan detail aduan (dengan autentikasi)
   - `PUT /api/v1/complaints/{id}` - Memperbarui status aduan (dengan autentikasi)

3. **Berita**
   - `GET /api/v1/news` - Mendapatkan daftar berita
   - `GET /api/v1/news/{id}` - Mendapatkan detail berita
   - `GET /api/v1/news/category/{category}` - Mendapatkan berita berdasarkan kategori
   - `GET /api/v1/news/popular` - Mendapatkan berita populer

4. **Media Sosial**
   - `GET /api/v1/social-media` - Mendapatkan daftar media sosial
   - `GET /api/v1/social-media/{id}` - Mendapatkan detail media sosial
   - `GET /api/v1/social-media/platform/{platform}` - Mendapatkan media sosial berdasarkan platform

### Struktur Data API

#### Galeri Berita
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Galeri Wisata Alam",
      "description": "Kumpulan foto-foto indah wisata alam di Kabupaten Muna Barat",
      "news_id": 5,
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z",
      "news": {
        "id": 5,
        "title": "Wisata Alam Muna Barat",
        "slug": "wisata-alam-muna-barat"
      },
      "images": [
        {
          "id": 1,
          "gallery_id": 1,
          "image_path": "galleries/image1.jpg",
          "caption": "Pantai indah di Muna Barat",
          "sort_order": 0,
          "created_at": "2025-09-09T10:00:00.000000Z",
          "updated_at": "2025-09-09T10:00:00.000000Z"
        }
      ]
    }
  ]
}
```

## Task untuk Pengembangan Landing Page Portal Berita dengan Vue.js

### Task Pengembangan Awal
1. [x] Membuat project Vue.js baru dengan Vite
2. [x] Mengatur struktur direktori komponen
3. [x] Menginstal dependensi yang diperlukan (Axios, Vue Router, Vuex/Pinia)
4. [x] Membuat layout dasar dengan header, main, dan footer
5. [x] Mengatur routing dasar untuk halaman utama

### Task Integrasi API
1. [x] Membuat service API untuk mengakses endpoint berita
2. [x] Membuat service API untuk mengakses endpoint galeri
3. [x] Membuat service API untuk mengakses endpoint media sosial
4. [x] Membuat service API untuk mengakses endpoint aduan masyarakat
5. [x] Mengimplementasikan state management untuk data API

### Task Pengembangan Komponen Header
1. [x] Membuat komponen HeaderNavigation
2. [x] Mengimplementasikan menu navigasi utama
3. [x] Menambahkan search bar dengan fungsi pencarian
4. [x] Menambahkan logo website
5. [x] Membuat responsive design untuk mobile

### Task Pengembangan Hero Section
1. [x] Membuat komponen HeroCarousel
2. [x] Mengintegrasikan dengan API hero carousel
3. [x] Menambahkan navigasi carousel (prev/next)
4. [x] Mengimplementasikan autoplay dengan kontrol
5. [x] Menambahkan indikator halaman carousel

### Task Pengembangan Section Berita Terbaru
1. [x] Membuat komponen NewsList
2. [x] Mengintegrasikan dengan API berita
3. [x] Menambahkan tampilan grid untuk berita
4. [x] Mengimplementasikan pagination
5. [x] Menambahkan filter kategori

### Task Pengembangan Section Media Sosial
1. [x] Membuat komponen SocialMediaList
2. [x] Mengintegrasikan dengan API media sosial
3. [x] Menambahkan embed video YouTube/TikTok
4. [x] Mengimplementasikan thumbnail dengan link
5. [x] Menambahkan filter berdasarkan platform

### Task Pengembangan Section Galeri Foto
1. [x] Membuat komponen PhotoGallery
2. [x] Mengintegrasikan dengan API galeri berita
3. [x] Mengimplementasikan masonry grid layout
4. [x] Menambahkan lightbox untuk tampilan detail
5. [x] Mengoptimalkan loading gambar

### Task Pengembangan Form Aduan Masyarakat
1. [x] Membuat komponen ComplaintForm
2. [x] Mengimplementasikan validasi form
3. [x] Menambahkan input untuk file upload
4. [x] Mengintegrasikan dengan API aduan masyarakat
5. [x] Menambahkan notifikasi sukses/error

### Task Pengembangan Footer
1. [x] Membuat komponen Footer
2. [x] Menambahkan informasi kontak
3. [x] Mengimplementasikan link ke halaman penting
4. [x] Menambahkan social media links
5. [x] Membuat responsive design untuk mobile

### Task Optimasi Performa dan UX
1. [x] Mengimplementasikan lazy loading untuk gambar
2. [x] Menambahkan loading state untuk setiap section
3. [x] Mengoptimalkan bundle size dengan code splitting
4. [x] Menambahkan error handling global
5. [x] Mengimplementasikan caching untuk data API

### Task Responsive Design
1. [x] Mengatur breakpoints untuk berbagai ukuran layar
2. [x] Membuat layout grid responsif
3. [x] Mengoptimasi touch interaction untuk mobile
4. [x] Menyesuaikan ukuran font dan elemen untuk mobile
5. [x] Menguji tampilan di berbagai perangkat

### Task Testing dan Deployment
1. [x] Melakukan testing integrasi API
2. [x] Melakukan testing responsive design
3. [x] Melakukan optimasi performa
4. [ ] Menyiapkan build production
5. [ ] Deploy ke server development

## Catatan Pengembangan
- [x] Fitur persetujuan kontributor telah diimplementasikan menggunakan filament-breezy
- [x] API untuk frontend Vue.js telah dibuat dan dapat digunakan untuk mengakses data galeri berita
- [x] Tampilan galeri di frontend sudah dikembangkan menggunakan Vue.js
- [x] Pengembangan API untuk fitur lainnya telah selesai sesuai kebutuhan frontend
- [x] Dokumentasi API lengkap tersedia di file API.md
- [x] Factory dan Seeder telah dibuat untuk memudahkan pengembangan dan testing
- [x] Landing page portal berita telah dibangun dengan Vue.js
- [x] Semua komponen telah diintegrasikan dengan API yang tersedia
- [x] Testing dan optimasi performa telah dilakukan

## Desain Form Berita (Mirip WordPress)
Form berita dirancang dengan tampilan dan struktur yang mirip dengan WordPress, terdiri dari:

### Area Konten Utama
- **Judul Berita**: Field untuk memasukkan judul berita
- **Isi Berita**: Editor markdown untuk menulis konten berita

### Sidebar Metabox
- **Publikasi**: Berisi pengaturan status berita, tanggal publikasi, penulis, dan tombol pratinjau
- **Gambar Unggulan**: Untuk mengunggah featured image
- **Kategori**: Mengatur kategori berita
- **Tag**: Mengatur tag berita
- **Ringkasan**: Field untuk excerpt berita
- **Permalink**: Menampilkan dan mengatur slug URL

## Pengembangan Frontend Vue.js

### Komponen Utama yang Telah Dikembangkan
1. **HeaderNavigation**: Komponen navigasi utama dengan menu dan search bar
2. **HeroCarousel**: Carousel untuk menampilkan konten hero dengan autoplay dan kontrol
3. **NewsList**: Daftar berita dengan tampilan grid dan pagination
4. **SocialMediaList**: Tampilan konten media sosial dengan embed video
5. **PhotoGallery**: Galeri foto dengan layout masonry dan lightbox
6. **ComplaintForm**: Formulir aduan masyarakat dengan validasi
7. **Footer**: Footer dengan informasi kontak dan link sosial media

### Fitur yang Telah Diimplementasikan
- Responsive design untuk semua ukuran layar
- Lazy loading untuk optimasi performa
- State management dengan Vuex/Pinia
- Integrasi penuh dengan semua endpoint API backend
- Error handling global
- Caching untuk data API

## Rencana Pengembangan
1. [x] Selesaikan admin panel dengan fungsi lengkap dan UI/UX interaktif
2. [x] Setelah admin panel selesai, lanjutkan ke pengembangan frontend Vue.js
3. [x] Implementasikan tampilan galeri di frontend menggunakan Vue.js dengan mengakses API yang telah dibuat
4. [x] Lanjutkan pengembangan API untuk fitur lainnya sesuai kebutuhan frontend
5. [x] Membangun landing page portal berita yang profesional dan modern dengan Vue.js
6. [x] Mengintegrasikan semua komponen dengan API yang tersedia
7. [x] Melakukan testing dan optimasi performa
8. [ ] Menyiapkan build production
9. [ ] Deploy ke server development

## Task untuk Implementasi Panel dan Pembatasan Menu Berdasarkan Role

### 1. Analisis Kebutuhan dan Perencanaan
- [ ] Menganalisis permission dan akses yang diperlukan untuk setiap role (super_admin, editor, penulis, kontributor)
- [ ] Mendokumentasikan struktur menu dan submenu yang tersedia untuk setiap role
- [ ] Mengidentifikasi resource Filament yang perlu dibatasi aksesnya

### 2. Implementasi Role-Based Access Control
- [ ] Membuat permission khusus untuk setiap role berdasarkan deskripsi yang ada
- [ ] Mengimplementasikan guard dan middleware untuk membatasi akses ke menu dan fitur
- [ ] Menambahkan pengecekan role pada setiap menu item di panel admin

### 3. Pembatasan Menu untuk Role Editor
- [ ] Membatasi akses menu editor hanya untuk fitur yang sesuai dengan deskripsi:
  - Mengelola berita (CRUD)
  - Mengelola program kerja
  - Mengelola galeri media sosial
- [ ] Menyembunyikan menu dan submenu yang tidak relevan dengan role editor
- [ ] Membatasi akses ke resource tertentu berdasarkan permission

### 4. Pembatasan Menu untuk Role Penulis
- [ ] Membatasi akses menu penulis hanya untuk:
  - Membuat dan mengedit berita yang ditulisnya sendiri
  - Melihat daftar berita yang telah dibuat
- [ ] Menyembunyikan menu untuk mengelola program kerja dan galeri media sosial
- [ ] Membatasi akses ke resource berita hanya pada item yang dibuat sendiri

### 5. Pembatasan Menu untuk Role Kontributor
- [ ] Membatasi akses menu kontributor hanya untuk:
  - Mengirim berita untuk direview
  - Melihat status berita yang telah dikirim
- [ ] Menyembunyikan menu untuk mengedit atau menghapus berita
- [ ] Membatasi akses ke fitur lain selain pengiriman berita

### 6. Testing dan Verifikasi
- [ ] Melakukan testing akses menu untuk setiap role
- [ ] Memverifikasi bahwa setiap role hanya dapat mengakses menu dan fitur yang sesuai
- [ ] Memastikan tidak ada bypass permission atau akses yang tidak seharusnya

### 7. Dokumentasi
- [ ] Mendokumentasikan permission dan akses yang telah diimplementasikan
- [ ] Menambahkan informasi ke user guide untuk admin
- [ ] Memperbarui dokumentasi teknis untuk developer selanjutnya
