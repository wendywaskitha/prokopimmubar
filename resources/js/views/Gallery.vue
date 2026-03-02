<template>
  <div class="gallery-page">
    <!-- Hero Header -->
    <div class="gallery-hero">
      <div class="gallery-hero-content">
        <h1>Galeri Foto</h1>
        <p>Dokumentasi visual kegiatan dan keindahan Kabupaten Muna Barat</p>
      </div>
    </div>

    <div class="gallery-container">
      <!-- Filters Section -->
      <div class="gallery-filters">
        <div class="filter-group">
          <div class="filter-item">
            <label for="category-select">Kategori</label>
            <select
              id="category-select"
              v-model="selectedCategory"
              class="filter-select"
              @change="handleCategoryChange"
              :disabled="loading"
            >
              <option value="">Semua Kategori</option>
              <option v-for="category in categories" :key="category.id" :value="category.slug">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="filter-item">
            <label for="search-input">Cari Galeri</label>
            <div class="search-input-group">
              <input
                id="search-input"
                type="text"
                v-model.trim="searchQuery"
                placeholder="Cari galeri foto..."
                class="search-input"
                @keyup.enter="handleSearch"
                @input="debounceSearch"
                :disabled="loading"
              />
              <button
                class="search-btn"
                @click="handleSearch"
                :disabled="loading"
                aria-label="Cari galeri"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="filter-actions">
          <button
            class="filter-reset"
            @click="resetFilters"
            :disabled="loading"
          >
            Reset Filter
          </button>
          <div class="view-options">
            <select
              class="sort-select"
              v-model="sortBy"
              :disabled="loading"
              aria-label="Urutkan berdasarkan"
            >
              <option value="latest">Terbaru</option>
              <option value="popular">Terpopuler</option>
              <option value="oldest">Terlama</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="gallery-content">
        <!-- Main Gallery -->
        <div class="gallery-main">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <div class="skeleton-grid">
              <div v-for="i in 6" :key="`skeleton-${i}`" class="skeleton-card">
                <div class="skeleton-image"></div>
                <div class="skeleton-content">
                  <div class="skeleton-line short"></div>
                  <div class="skeleton-line"></div>
                  <div class="skeleton-line long"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <p>{{ error }}</p>
            <button @click="retryLoadGallery" class="retry-btn">Coba Lagi</button>
          </div>

          <!-- Gallery Grid -->
          <div v-else>
            <!-- Featured Gallery -->
            <div class="featured-gallery" v-if="featuredGallery && !searchQuery">
              <h2 class="section-title">Galeri Pilihan</h2>
              <router-link :to="`/gallery/${featuredGallery.id}`" class="featured-item-link">
                <div class="featured-item">
                  <img
                    :src="featuredGallery.image || defaultImage"
                    :alt="featuredGallery.title || 'Galeri Pilihan'"
                    loading="lazy"
                    @error="handleImageError"
                  />
                  <div class="featured-overlay">
                    <div class="featured-content">
                      <span class="featured-badge">Unggulan</span>
                      <h3>{{ featuredGallery.title || 'Galeri Pilihan' }}</h3>
                      <p>{{ featuredGallery.description || 'Dokumentasi visual pilihan' }}</p>
                      <button class="view-gallery-btn" aria-label="Lihat galeri pilihan">
                        Lihat Galeri
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </router-link>
            </div>

            <!-- Results Info -->
            <div class="results-section">
              <div class="results-info">
                <h2 class="section-title">
                  {{ searchQuery ? `Hasil Pencarian: "${searchQuery}"` : 'Semua Galeri' }}
                </h2>
                <p>Menampilkan {{ galleryItems.length }} dari {{ totalGalleries }} koleksi foto</p>
              </div>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-grid" v-if="galleryItems.length > 0">
              <div class="gallery-item" v-for="item in galleryItems" :key="`gallery-${item.id}`">
                <router-link :to="`/gallery/${item.id}`" class="gallery-item-link">
                  <div class="item-image">
                    <img
                      :src="item.image || defaultImage"
                      :alt="item.title || `Galeri ${item.id}`"
                      loading="lazy"
                      @error="handleImageError"
                    />
                    <div class="image-count" v-if="item.count && item.count > 0">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21,15 16,10 5,21"/>
                      </svg>
                      {{ item.count }} Foto
                    </div>
                    <div class="item-overlay">
                      <div class="overlay-content">
                        <button class="view-btn" :aria-label="`Lihat galeri ${item.title}`">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                          </svg>
                          Lihat
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="item-content">
                    <div class="item-meta">
                      <span class="category-tag" :class="getCategoryClass(item.category)">
                        {{ item.category || 'Umum' }}
                      </span>
                      <span class="item-date">{{ formatDate(item.date) }}</span>
                    </div>
                    <h3 class="item-title">{{ item.title || `Galeri Foto ${item.id}` }}</h3>
                    <p class="item-description">{{ truncateDescription(item.description || 'Koleksi foto kegiatan dan dokumentasi visual dari berbagai aktivitas di Kabupaten Muna Barat.', 150) }}</p>
                    <div class="item-stats">
                      <span class="stat-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                        {{ formatViews(item.views || 0) }} views
                      </span>
                      <span class="stat-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        {{ item.likes || 0 }} likes
                      </span>
                    </div>
                  </div>
                </router-link>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="galleryItems.length === 0 && !loading" class="empty-state">
              <div class="empty-icon">🖼️</div>
              <h3>{{ searchQuery ? 'Tidak Ada Hasil Pencarian' : 'Tidak Ada Galeri Ditemukan' }}</h3>
              <p>{{ searchQuery ? 'Coba ubah kata kunci pencarian Anda' : 'Coba ubah filter atau kata kunci pencarian Anda' }}</p>
              <button @click="resetFilters" class="reset-filters-btn">Reset Filter</button>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper" v-if="totalPages > 1 && galleryItems.length > 0">
              <nav class="pagination" aria-label="Navigasi halaman galeri">
                <button
                  class="page-btn prev"
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  aria-label="Halaman sebelumnya"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 18l-6-6 6-6"/>
                  </svg>
                  Sebelumnya
                </button>

                <div class="page-numbers">
                  <template v-for="page in visiblePages" :key="`page-${page}`">
                    <button
                      v-if="page !== '...'"
                      class="page-number"
                      :class="{ active: currentPage === page }"
                      @click="changePage(page)"
                      :aria-label="`Halaman ${page}`"
                      :aria-current="currentPage === page ? 'page' : null"
                    >
                      {{ page }}
                    </button>
                    <span v-else class="page-dots">...</span>
                  </template>
                </div>

                <button
                  class="page-btn next"
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  aria-label="Halaman selanjutnya"
                >
                  Selanjutnya
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                  </svg>
                </button>
              </nav>

              <div class="pagination-info">
                <p>Halaman {{ currentPage }} dari {{ totalPages }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <aside class="gallery-sidebar">
          <!-- Quick Stats -->
          <div class="sidebar-widget stats-widget">
            <h3 class="widget-title">Statistik Galeri</h3>
            <div class="stats-grid">
              <div class="stat-item">
                <div class="stat-number">{{ totalGalleries || 0 }}</div>
                <div class="stat-label">Total Galeri</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">{{ totalPhotos || 0 }}</div>
                <div class="stat-label">Total Foto</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">{{ formatViews(totalViews || 0) }}</div>
                <div class="stat-label">Total Views</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">{{ thisMonth || 0 }}</div>
                <div class="stat-label">Bulan Ini</div>
              </div>
            </div>
          </div>

          <!-- Popular Categories -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Kategori</h3>
            <div class="category-list">
              <div
                class="category-item"
                v-for="category in categories"
                :key="`cat-${category.id}`"
                @click="filterByCategory(category.slug)"
                @keyup.enter="filterByCategory(category.slug)"
                tabindex="0"
                role="button"
                :aria-label="`Filter berdasarkan kategori ${category.name}`"
              >
                <span class="category-name">{{ category.name }}</span>
                <span class="category-count">{{ category.news_count || category.count || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Recent Galleries -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Galeri Terbaru</h3>
            <div class="recent-list">
              <router-link
                v-for="item in recentGalleries"
                :key="`recent-${item.id}`"
                :to="`/gallery/${item.id}`"
                class="recent-item-link"
              >
                <div class="recent-item">
                  <div class="recent-image">
                    <img
                      :src="item.thumbnail || defaultThumbnail"
                      :alt="item.title || 'Galeri terbaru'"
                      loading="lazy"
                      @error="handleImageError"
                    />
                  </div>
                  <div class="recent-content">
                    <h4>{{ item.title || 'Galeri Terbaru' }}</h4>
                    <span class="recent-date">{{ item.date || 'Tanggal tidak tersedia' }}</span>
                  </div>
                </div>
              </router-link>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'Gallery',
  data() {
    return {
      categories: [],
      selectedCategory: '',
      searchQuery: '',
      sortBy: 'latest',
      currentPage: 1,
      totalPages: 1,
      loading: false,
      error: null,
      galleryItems: [],
      featuredGallery: null,
      recentGalleries: [],
      totalGalleries: 0,
      totalPhotos: 0,
      totalViews: 0,
      thisMonth: 0,
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 12,
        total: 0
      },
      viewMode: 'grid',
      searchTimeout: null,
      retryCount: 0,
      maxRetries: 3,
      defaultImage: 'https://placehold.co/400x300?text=Galeri+Foto',
      defaultThumbnail: 'https://placehold.co/60x60?text=Thumb'
    }
  },
  computed: {
    visiblePages() {
      const pages = [];
      const current = this.currentPage;
      const total = this.totalPages;

      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i);
        }
      } else {
        if (current <= 4) {
          for (let i = 1; i <= 5; i++) pages.push(i);
          pages.push('...');
          pages.push(total);
        } else if (current >= total - 3) {
          pages.push(1);
          pages.push('...');
          for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
          pages.push(1);
          pages.push('...');
          for (let i = current - 1; i <= current + 1; i++) pages.push(i);
          pages.push('...');
          pages.push(total);
        }
      }

      return pages;
    }
  },
  async mounted() {
    try {
      await this.loadCategories();
      await this.loadGallery();
    } catch (error) {
      console.error('Error during component initialization:', error);
      this.error = 'Gagal memuat halaman galeri';
    }
  },
  beforeUnmount() {
    // Cleanup timeout
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
  },
  watch: {
    sortBy() {
      this.currentPage = 1;
      this.loadGallery();
    }
  },
  methods: {
    async loadCategories() {
      try {
        const response = await api.getCategories(true);
        if (response?.data?.success) {
          this.categories = (response.data.data || []).map(category => ({
            ...category,
            count: category.count || 0
          }));
        } else {
          this.setFallbackCategories();
        }
      } catch (error) {
        console.error('Error loading categories:', error);
        this.setFallbackCategories();
      }
    },

    setFallbackCategories() {
      this.categories = [
        { id: 1, name: 'Berita Umum', slug: 'berita-umum', count: 24 },
        { id: 2, name: 'Pemerintahan', slug: 'pemerintahan', count: 18 },
        { id: 3, name: 'Ekonomi', slug: 'ekonomi', count: 15 },
        { id: 4, name: 'Pendidikan', slug: 'pendidikan', count: 12 },
        { id: 5, name: 'Kesehatan', slug: 'kesehatan', count: 8 },
        { id: 6, name: 'Olahraga', slug: 'olahraga', count: 10 },
        { id: 7, name: 'Pariwisata', slug: 'wisata', count: 14 },
        { id: 8, name: 'Budaya', slug: 'budaya', count: 9 },
        { id: 9, name: 'Pertanian', slug: 'pertanian', count: 7 },
        { id: 10, name: 'Peternakan', slug: 'peternakan', count: 5 },
        { id: 11, name: 'Perikanan', slug: 'perikanan', count: 6 }
      ];
    },

    async loadGallery() {
      this.loading = true;
      this.error = null;

      try {
        const params = {
          page: this.currentPage,
          search: this.searchQuery.trim(),
          sort_by: this.sortBy,
          per_page: this.pagination.per_page
        };

        if (this.selectedCategory) {
          params.category = this.selectedCategory;
        }

        const response = await api.getGalleries(params, true);

        if (response?.data?.success) {
          this.galleryItems = (response.data.data || []).map(gallery => ({
            id: gallery.id,
            title: gallery.title || `Galeri ${gallery.id}`,
            description: gallery.description || 'Koleksi foto kegiatan dan dokumentasi visual',
            image: this.getImageUrl(gallery.images),
            category: this.getCategoryName(gallery),
            date: gallery.created_at,
            count: gallery.images?.length || 0,
            views: gallery.views || Math.floor(Math.random() * 1000) + 100,
            likes: gallery.likes || Math.floor(Math.random() * 50) + 10
          }));

          // Update pagination safely
          if (response.data.pagination) {
            this.pagination = { ...this.pagination, ...response.data.pagination };
            this.totalPages = response.data.pagination.last_page || 1;
            this.totalGalleries = response.data.pagination.total || 0;
          }

          // Set featured gallery
          if (this.galleryItems.length > 0 && !this.searchQuery) {
            this.featuredGallery = { ...this.galleryItems[0] };
          } else {
            this.featuredGallery = null;
          }

          // Set recent galleries
          this.setRecentGalleries();

          // Update stats
          this.updateStats();

          this.retryCount = 0; // Reset retry count on success
        } else {
          throw new Error(response?.data?.message || 'Gagal memuat data galeri');
        }
      } catch (error) {
        console.error('Error loading gallery:', error);
        this.handleLoadError(error);
      } finally {
        this.loading = false;
      }
    },

    getImageUrl(images) {
      if (images && images.length > 0) {
        const imagePath = images[0].image_path;
        return imagePath.startsWith('/storage/') ? imagePath : `/storage/${imagePath}`;
      }
      return null;
    },

    getCategoryName(gallery) {
      if (gallery.news?.categories?.length > 0) {
        return gallery.news.categories[0].name;
      }
      return 'Umum';
    },

    setRecentGalleries() {
      this.recentGalleries = this.galleryItems.slice(0, 5).map(item => ({
        id: item.id,
        title: item.title,
        thumbnail: item.image || this.defaultThumbnail,
        date: this.formatDate(item.date)
      }));
    },

    updateStats() {
      this.totalPhotos = this.galleryItems.reduce((sum, item) => sum + (item.count || 0), 0);
      this.totalViews = this.galleryItems.reduce((sum, item) => sum + (item.views || 0), 0);

      // Calculate this month's galleries
      const currentMonth = new Date().getMonth();
      const currentYear = new Date().getFullYear();
      this.thisMonth = this.galleryItems.filter(item => {
        const itemDate = new Date(item.date);
        return itemDate.getMonth() === currentMonth && itemDate.getFullYear() === currentYear;
      }).length;
    },

    handleLoadError(error) {
      if (this.retryCount < this.maxRetries) {
        this.error = `Gagal memuat galeri. Mencoba lagi... (${this.retryCount + 1}/${this.maxRetries})`;
        setTimeout(() => this.retryLoadGallery(), 2000);
      } else {
        this.error = 'Terjadi kesalahan saat memuat galeri. Silakan coba lagi nanti.';
      }
    },

    retryLoadGallery() {
      this.retryCount++;
      this.loadGallery();
    },

    changePage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.currentPage = page;
        this.loadGallery();
        this.scrollToTop();
      }
    },

    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.changePage(this.currentPage + 1);
      }
    },

    prevPage() {
      if (this.currentPage > 1) {
        this.changePage(this.currentPage - 1);
      }
    },

    resetFilters() {
      this.selectedCategory = '';
      this.searchQuery = '';
      this.currentPage = 1;
      this.retryCount = 0;
      this.loadGallery();
    },

    filterByCategory(category) {
      this.selectedCategory = category;
      this.currentPage = 1;
      this.loadGallery();
    },

    debounceSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }

      this.searchTimeout = setTimeout(() => {
        this.handleSearch();
      }, 500);
    },

    handleSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      this.currentPage = 1;
      this.loadGallery();
    },

    handleCategoryChange() {
      this.currentPage = 1;
      this.loadGallery();
    },

    getCategoryClass(category) {
      if (!category) return 'category-default';
      return `category-${category.toLowerCase().replace(/\s+/g, '-')}`;
    },

    formatDate(dateString) {
      if (!dateString) return 'Tanggal tidak tersedia';

      try {
        const options = {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        };
        return new Date(dateString).toLocaleDateString('id-ID', options);
      } catch (error) {
        return 'Tanggal tidak valid';
      }
    },

    formatViews(views) {
      const numViews = Number(views) || 0;
      if (numViews >= 1000000) {
        return (numViews / 1000000).toFixed(1) + 'M';
      } else if (numViews >= 1000) {
        return (numViews / 1000).toFixed(1) + 'k';
      }
      return numViews.toString();
    },

    truncateDescription(text, maxLength = 150) {
      if (!text || text.length <= maxLength) return text;
      return text.substring(0, maxLength).trim() + '...';
    },

    handleImageError(event) {
      const target = event.target;
      if (target.src !== this.defaultImage && target.src !== this.defaultThumbnail) {
        target.src = target.width > 60 ? this.defaultImage : this.defaultThumbnail;
      }
    },

    scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }
  }
}
</script>

<style scoped>
.gallery-page {
  min-height: 100vh;
  background: #f8fffe;
}

/* Hero Section */
.gallery-hero {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 4rem 0 3rem;
  text-align: center;
}

.gallery-hero-content h1 {
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.gallery-hero-content p {
  font-size: 1.2rem;
  opacity: 0.95;
  margin: 0;
}

/* Container */
.gallery-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Filters */
.gallery-filters {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  padding: 2rem;
  margin: -2rem 0 3rem;
  position: relative;
  z-index: 10;
}

.filter-group {
  display: flex;
  gap: 2rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.filter-item {
  flex: 1;
  min-width: 250px;
}

.filter-item label {
  display: block;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.filter-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  background: white;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.filter-select:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: #f9fafb;
}

.search-input-group {
  display: flex;
  position: relative;
}

.search-input {
  flex: 1;
  padding: 0.75rem 3rem 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-input:disabled {
  opacity: 0.6;
  background: #f9fafb;
}

.search-btn {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  background: #10b981;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-btn:hover:not(:disabled) {
  background: #059669;
}

.search-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.filter-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filter-reset {
  background: #f3f4f6;
  color: #4b5563;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-reset:hover:not(:disabled) {
  background: #e5e7eb;
}

.filter-reset:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.sort-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  background: white;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.sort-select:focus {
  outline: none;
  border-color: #10b981;
}

.sort-select:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background: #f9fafb;
}

/* Content Layout */
.gallery-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 3rem;
  align-items: start;
}

/* Skeleton Loaders */
.skeleton-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.skeleton-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
}

.skeleton-image {
  height: 200px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

.skeleton-content {
  padding: 1.5rem;
}

.skeleton-line {
  height: 16px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  margin-bottom: 12px;
  border-radius: 4px;
}

.skeleton-line.short {
  width: 60%;
}

.skeleton-line.long {
  width: 90%;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Featured Gallery */
.featured-gallery {
  margin-bottom: 4rem;
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 2rem;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 2px;
}

.featured-item-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.featured-item {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  aspect-ratio: 21/9;
  cursor: pointer;
  box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15);
}

.featured-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.featured-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.7) 0%, rgba(5, 150, 105, 0.8) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.featured-item:hover .featured-overlay {
  opacity: 1;
}

.featured-item:hover img {
  transform: scale(1.05);
}

.featured-content {
  text-align: center;
  color: white;
  max-width: 600px;
  padding: 2rem;
}

.featured-badge {
  background: rgba(255,255,255,0.2);
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 1rem;
  display: inline-block;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
}

.featured-content h3 {
  font-size: 2.5rem;
  font-weight: 800;
  margin: 0 0 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.featured-content p {
  font-size: 1.1rem;
  margin-bottom: 2rem;
  opacity: 0.95;
}

.view-gallery-btn {
  background: rgba(255,255,255,0.2);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.view-gallery-btn:hover {
  background: rgba(255,255,255,0.3);
  gap: 0.75rem;
}

/* Results Section */
.results-section {
  margin-bottom: 2rem;
}

.results-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.results-info p {
  margin: 0;
  color: #6b7280;
  font-weight: 500;
}

/* Gallery Grid */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.gallery-item-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.gallery-item {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.gallery-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.item-image {
  position: relative;
  overflow: hidden;
  aspect-ratio: 4/3;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.gallery-item:hover .item-image img {
  transform: scale(1.1);
}

.image-count {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(0,0,0,0.7);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  backdrop-filter: blur(10px);
}

.item-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.8) 0%, rgba(5, 150, 105, 0.9) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery-item:hover .item-overlay {
  opacity: 1;
}

.view-btn {
  background: rgba(255,255,255,0.2);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.view-btn:hover {
  background: rgba(255,255,255,0.3);
}

.item-content {
  padding: 1.5rem;
}

.item-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.category-tag {
  padding: 0.3rem 0.8rem;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.category-berita-umum { background: #dbeafe; color: #1e40af; }
.category-pemerintahan { background: #fef3c7; color: #d97706; }
.category-ekonomi { background: #fce7f3; color: #be185d; }
.category-pendidikan { background: #dcfce7; color: #166534; }
.category-kesehatan { background: #e0e7ff; color: #4338ca; }
.category-olahraga { background: #fef2f2; color: #dc2626; }
.category-wisata { background: #ecfdf5; color: #059669; }
.category-budaya { background: #fdf4ff; color: #a21caf; }
.category-pertanian { background: #f0fdf4; color: #15803d; }
.category-peternakan { background: #fffbeb; color: #d97706; }
.category-perikanan { background: #eff6ff; color: #2563eb; }
.category-default { background: #f3f4f6; color: #4b5563; }

.item-date {
  color: #9ca3af;
  font-size: 0.85rem;
}

.item-title {
  margin: 0 0 1rem;
  font-size: 1.2rem;
  font-weight: 600;
  color: #1f2937;
  line-height: 1.4;
}

.item-description {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}

.item-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #9ca3af;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

/* States */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
}

.error-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #dc2626;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.retry-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 1rem;
  transition: background-color 0.3s ease;
}

.retry-btn:hover {
  background: #b91c1c;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.reset-filters-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 1rem;
  transition: background-color 0.3s ease;
}

.reset-filters-btn:hover {
  background: #059669;
}

/* Pagination */
.pagination-wrapper {
  margin-top: 3rem;
  text-align: center;
}

.pagination {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  padding: 1rem;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.page-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border: none;
  background: transparent;
  color: #6b7280;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.page-btn:hover:not(:disabled) {
  background: #f3f4f6;
  color: #10b981;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 0.25rem;
}

.page-number {
  width: 40px;
  height: 40px;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #6b7280;
  font-weight: 500;
}

.page-number:hover {
  background: #f3f4f6;
  color: #10b981;
}

.page-number.active {
  background: #10b981;
  color: white;
}

.page-dots {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
}

.pagination-info {
  margin-top: 1rem;
  color: #6b7280;
  font-size: 0.9rem;
}

/* Sidebar */
.gallery-sidebar {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.sidebar-widget {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.widget-title {
  margin: 0 0 1.5rem;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  position: relative;
  padding-bottom: 0.75rem;
}

.widget-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 3px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 2px;
}

/* Stats Widget */
.stats-widget {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.stats-widget .widget-title::after {
  background: rgba(255,255,255,0.3);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.stats-grid .stat-item {
  text-align: center;
  padding: 1rem;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.stat-number {
  font-size: 2rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.85rem;
  opacity: 0.9;
}

/* Category List */
.category-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  background: #f9fafb;
  border-radius: 10px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.category-item:hover,
.category-item:focus {
  background: #f3f4f6;
  transform: translateX(3px);
  outline: none;
}

.category-name {
  color: #1f2937;
  font-weight: 500;
}

.category-count {
  background: #10b981;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

/* Recent List */
.recent-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.recent-item-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.recent-item {
  display: flex;
  gap: 1rem;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f3f4f6;
  transition: background-color 0.3s ease;
}

.recent-item:hover {
  background-color: #f9fafb;
}

.recent-item:last-child {
  border-bottom: none;
}

.recent-image {
  flex-shrink: 0;
}

.recent-image img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 8px;
}

.recent-content h4 {
  margin: 0 0 0.25rem;
  font-size: 0.9rem;
  color: #1f2937;
  line-height: 1.3;
}

.recent-date {
  font-size: 0.8rem;
  color: #9ca3af;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .gallery-content {
    grid-template-columns: 1fr 300px;
    gap: 2rem;
  }
}

@media (max-width: 992px) {
  .gallery-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .gallery-sidebar {
    order: -1;
  }

  .sidebar-widget {
    padding: 1.5rem;
  }

  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .gallery-hero-content h1 {
    font-size: 2.5rem;
  }

  .gallery-container {
    padding: 0 1rem;
  }

  .gallery-filters {
    padding: 1.5rem;
    margin: -1.5rem 0 2rem;
  }

  .filter-group {
    flex-direction: column;
    gap: 1rem;
  }

  .filter-item {
    min-width: auto;
  }

  .filter-actions {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .gallery-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }

  .skeleton-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }

  .featured-content h3 {
    font-size: 2rem;
  }

  .pagination {
    flex-wrap: wrap;
    gap: 0.25rem;
  }

  .page-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .gallery-hero-content h1 {
    font-size: 2rem;
  }

  .gallery-grid {
    grid-template-columns: 1fr;
  }

  .skeleton-grid {
    grid-template-columns: 1fr;
  }

  .item-content {
    padding: 1rem;
  }

  .featured-content {
    padding: 1.5rem 1rem;
  }

  .featured-content h3 {
    font-size: 1.5rem;
  }
}
</style>
