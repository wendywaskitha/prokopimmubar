# Dokumentasi API Backend Warta Daerah Kabupaten Muna Barat

## Base URL
```
http://localhost:8000/api/v1
```

## Headers
Untuk semua request API, gunakan headers berikut:
```
Accept: application/json
Content-Type: application/json
```

## Endpoints

### 1. Hero Carousel

#### Mendapatkan semua hero carousel aktif
```
GET /heroes
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Selamat Datang di Warta Daerah Muna Barat",
      "description": "Portal informasi resmi Pemerintah Kabupaten Muna Barat",
      "image": "heroes/image1.jpg",
      "link": "/news/welcome",
      "is_active": true,
      "sort_order": 1,
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z"
    }
  ]
}
```

#### Mendapatkan detail hero carousel
```
GET /heroes/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Selamat Datang di Warta Daerah Muna Barat",
    "description": "Portal informasi resmi Pemerintah Kabupaten Muna Barat",
    "image": "heroes/image1.jpg",
    "link": "/news/welcome",
    "is_active": true,
    "sort_order": 1,
    "created_at": "2025-09-09T10:00:00.000000Z",
    "updated_at": "2025-09-09T10:00:00.000000Z"
  }
}
```

### 2. Galeri Berita

#### Mendapatkan semua galeri berita
```
GET /galleries
```

**Parameters:**
- `per_page` (optional): Jumlah item per halaman (default: 15)
- `page` (optional): Nomor halaman

**Response:**
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
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Mendapatkan detail galeri berita
```
GET /galleries/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
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
}
```

### 3. Aduan Masyarakat

#### Submit aduan masyarakat
```
POST /complaints
```

**Parameters:**
- `name` (required): Nama lengkap pelapor
- `email` (required): Email pelapor
- `phone` (required): Nomor telepon pelapor
- `category` (required): Kategori aduan
- `description` (required): Deskripsi aduan
- `document` (optional): Dokumen pendukung (PDF, DOC, DOCX)
- `photo` (optional): Foto pendukung (JPG, JPEG, PNG)

**Response:**
```json
{
  "success": true,
  "message": "Aduan berhasil dikirim",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "category": "Infrastruktur",
    "description": "Jalan rusak parah di daerah X",
    "status": "baru",
    "created_at": "2025-09-09T10:00:00.000000Z",
    "updated_at": "2025-09-09T10:00:00.000000Z"
  }
}
```

#### Mendapatkan daftar aduan
```
GET /complaints
```

**Parameters:**
- `per_page` (optional): Jumlah item per halaman (default: 15)
- `page` (optional): Nomor halaman
- `category` (optional): Filter berdasarkan kategori
- `status` (optional): Filter berdasarkan status
- `search` (optional): Pencarian berdasarkan nama atau deskripsi

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890",
      "category": "Infrastruktur",
      "description": "Jalan rusak parah di daerah X",
      "status": "baru",
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Mendapatkan detail aduan
```
GET /complaints/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "category": "Infrastruktur",
    "description": "Jalan rusak parah di daerah X",
    "status": "baru",
    "created_at": "2025-09-09T10:00:00.000000Z",
    "updated_at": "2025-09-09T10:00:00.000000Z"
  }
}
```

#### Memperbarui status aduan (untuk admin)
```
PUT /complaints/{id}
```

**Parameters:**
- `status` (required): Status aduan (baru, diproses, selesai, ditolak)
- `response` (optional): Tanggapan terhadap aduan

**Response:**
```json
{
  "success": true,
  "message": "Aduan berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "081234567890",
    "category": "Infrastruktur",
    "description": "Jalan rusak parah di daerah X",
    "status": "diproses",
    "created_at": "2025-09-09T10:00:00.000000Z",
    "updated_at": "2025-09-09T11:00:00.000000Z"
  }
}
```

### 4. Berita

#### Mendapatkan daftar berita
```
GET /news
```

**Parameters:**
- `per_page` (optional): Jumlah item per halaman (default: 15)
- `page` (optional): Nomor halaman
- `category` (optional): Filter berdasarkan slug kategori
- `search` (optional): Pencarian berdasarkan judul atau konten

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Berita Terbaru",
      "slug": "berita-terbaru",
      "content": "Isi berita...",
      "excerpt": "Ringkasan berita...",
      "featured_image": "news/image.jpg",
      "status": "published",
      "author_id": 1,
      "published_at": "2025-09-09T10:00:00.000000Z",
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z",
      "author": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com"
      },
      "categories": [
        {
          "id": 1,
          "name": "Umum",
          "slug": "umum"
        }
      ],
      "tags": [
        {
          "id": 1,
          "name": "penting",
          "slug": "penting"
        }
      ]
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Mendapatkan detail berita
```
GET /news/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Berita Terbaru",
    "slug": "berita-terbaru",
    "content": "Isi berita...",
    "excerpt": "Ringkasan berita...",
    "featured_image": "news/image.jpg",
    "status": "published",
    "author_id": 1,
    "published_at": "2025-09-09T10:00:00.000000Z",
    "created_at": "2025-09-09T10:00:00.000000Z",
    "updated_at": "2025-09-09T10:00:00.000000Z",
    "author": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com"
    },
    "categories": [
      {
        "id": 1,
        "name": "Umum",
        "slug": "umum"
      }
    ],
    "tags": [
      {
        "id": 1,
        "name": "penting",
        "slug": "penting"
      }
    ],
    "gallery": {
      "id": 1,
      "title": "Galeri Berita",
      "images": [
        {
          "id": 1,
          "image_path": "galleries/image1.jpg",
          "caption": "Gambar 1"
        }
      ]
    }
  }
}
```

#### Mendapatkan berita berdasarkan kategori
```
GET /news/category/{category}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Berita Terbaru",
      "slug": "berita-terbaru",
      "content": "Isi berita...",
      "excerpt": "Ringkasan berita...",
      "featured_image": "news/image.jpg",
      "status": "published",
      "author_id": 1,
      "published_at": "2025-09-09T10:00:00.000000Z",
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z",
      "author": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com"
      },
      "categories": [
        {
          "id": 1,
          "name": "Umum",
          "slug": "umum"
        }
      ],
      "tags": [
        {
          "id": 1,
          "name": "penting",
          "slug": "penting"
        }
      ]
    }
  ],
  "category": {
    "id": 1,
    "name": "Umum",
    "slug": "umum"
  },
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Mendapatkan berita populer
```
GET /news/popular
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Berita Terbaru",
      "slug": "berita-terbaru",
      "content": "Isi berita...",
      "excerpt": "Ringkasan berita...",
      "featured_image": "news/image.jpg",
      "status": "published",
      "author_id": 1,
      "published_at": "2025-09-09T10:00:00.000000Z",
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z",
      "author": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com"
      },
      "categories": [
        {
          "id": 1,
          "name": "Umum",
          "slug": "umum"
        }
      ],
      "tags": [
        {
          "id": 1,
          "name": "penting",
          "slug": "penting"
        }
      ]
    }
  ]
}
```

### 5. Media Sosial

#### Mendapatkan daftar media sosial
```
GET /social-media
```

**Parameters:**
- `per_page` (optional): Jumlah item per halaman (default: 15)
- `page` (optional): Nomor halaman
- `platform` (optional): Filter berdasarkan platform

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "platform": "youtube",
      "url": "https://youtube.com/watch?v=xxx",
      "embed_code": "<iframe>...</iframe>",
      "title": "Video Terbaru",
      "description": "Deskripsi video",
      "thumbnail": "social-media/thumbnail.jpg",
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Mendapatkan detail media sosial
```
GET /social-media/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "platform": "youtube",
    "url": "https://youtube.com/watch?v=xxx",
    "embed_code": "<iframe>...</iframe>",
    "title": "Video Terbaru",
    "description": "Deskripsi video",
    "thumbnail": "social-media/thumbnail.jpg",
    "created_at": "2025-09-09T10:00:00.000000Z",
    "updated_at": "2025-09-09T10:00:00.000000Z"
  }
}
```

#### Mendapatkan media sosial berdasarkan platform
```
GET /social-media/platform/{platform}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "platform": "youtube",
      "url": "https://youtube.com/watch?v=xxx",
      "embed_code": "<iframe>...</iframe>",
      "title": "Video Terbaru",
      "description": "Deskripsi video",
      "thumbnail": "social-media/thumbnail.jpg",
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

### 8. Agenda

#### Mendapatkan daftar agenda
```
GET /agendas
```

**Parameters:**
- `per_page` (optional): Jumlah item per halaman (default: 15)
- `page` (optional): Nomor halaman
- `start_date` (optional): Filter berdasarkan tanggal mulai (format: YYYY-MM-DD)
- `end_date` (optional): Filter berdasarkan tanggal selesai (format: YYYY-MM-DD)
- `search` (optional): Cari berdasarkan judul atau lokasi

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Rapat Koordinasi Bulanan",
      "description": "Rapat koordinasi rutin bulanan antara kepala daerah dan jajaran",
      "location": "Ruang Rapat Utama, Kantor Bupati",
      "start_date": "2025-09-20 09:00:00",
      "end_date": "2025-09-20 12:00:00",
      "is_published": true,
      "created_by": 1,
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z",
      "creator": {
        "id": 1,
        "name": "Admin Portal",
        "email": "admin@wartadaerah.munabarat.go.id"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### Mendapatkan detail agenda
```
GET /agendas/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Rapat Koordinasi Bulanan",
    "description": "Rapat koordinasi rutin bulanan antara kepala daerah dan jajaran",
    "location": "Ruang Rapat Utama, Kantor Bupati",
    "start_date": "2025-09-20 09:00:00",
    "end_date": "2025-09-20 12:00:00",
    "is_published": true,
    "created_by": 1,
    "created_at": "2025-09-09T10:00:00.000000Z",
    "updated_at": "2025-09-09T10:00:00.000000Z",
    "creator": {
      "id": 1,
      "name": "Admin Portal",
      "email": "admin@wartadaerah.munabarat.go.id"
    }
  }
}
```

#### Mendapatkan agenda yang akan datang
```
GET /agendas-upcoming
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Rapat Koordinasi Bulanan",
      "description": "Rapat koordinasi rutin bulanan antara kepala daerah dan jajaran",
      "location": "Ruang Rapat Utama, Kantor Bupati",
      "start_date": "2025-09-20 09:00:00",
      "end_date": "2025-09-20 12:00:00",
      "is_published": true,
      "created_by": 1,
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z",
      "creator": {
        "id": 1,
        "name": "Admin Portal",
        "email": "admin@wartadaerah.munabarat.go.id"
      }
    }
  ]
}
```

#### Mendapatkan agenda yang sedang berlangsung
```
GET /agendas-ongoing
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "title": "Pelatihan Digitalisasi Pelayanan",
      "description": "Pelatihan untuk meningkatkan kualitas pelayanan publik",
      "location": "Aula Pertemuan, Kantor Bupati",
      "start_date": "2025-09-15 08:00:00",
      "end_date": "2025-09-17 17:00:00",
      "is_published": true,
      "created_by": 1,
      "created_at": "2025-09-09T10:00:00.000000Z",
      "updated_at": "2025-09-09T10:00:00.000000Z",
      "creator": {
        "id": 1,
        "name": "Admin Portal",
        "email": "admin@wartadaerah.munabarat.go.id"
      }
    }
  ]
}
```