<template>
  <div class="news-page">
    <!-- Hero Header -->
    <div class="news-hero">
      <div class="news-hero-content">
        <h1>Berita Terbaru</h1>
        <p>Informasi terkini dan terpercaya dari Kabupaten Muna Barat</p>
      </div>
    </div>

    <div class="news-container">
      <!-- Filters Section -->
      <div class="news-filters">
        <div class="filter-group">
          <div class="filter-item">
            <label>Kategori</label>
            <select v-model="selectedCategory" class="filter-select">
              <option value="">Semua Kategori</option>
              <option v-for="category in categories" :key="category.id" :value="category.slug">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="filter-item">
            <label>Cari Berita</label>
            <div class="search-input-group">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Masukkan kata kunci..."
                class="search-input"
                @keyup.enter="handleSearch"
              >
              <button class="search-btn" @click="handleSearch">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="filter-actions">
          <button class="filter-reset" @click="resetFilters">Reset Filter</button>
          <div class="view-toggle">
            <button :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
              </svg>
            </button>
            <button :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="8" y1="6" x2="21" y2="6"></line>
                <line x1="8" y1="12" x2="21" y2="12"></line>
                <line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                <line x1="3" y1="18" x2="3.01" y2="18"></line>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="news-content">
        <!-- Main Content -->
        <div class="news-main">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <div v-if="viewMode === 'grid'" class="skeleton-grid">
              <div v-for="i in 6" :key="i" class="skeleton-card">
                <div class="skeleton-image"></div>
                <div class="skeleton-content">
                  <div class="skeleton-line short"></div>
                  <div class="skeleton-line"></div>
                  <div class="skeleton-line long"></div>
                  <div class="skeleton-line short"></div>
                </div>
              </div>
            </div>
            <div v-else class="skeleton-list">
              <div v-for="i in 4" :key="i" class="skeleton-list-item">
                <div class="skeleton-list-image"></div>
                <div class="skeleton-list-content">
                  <div class="skeleton-line short"></div>
                  <div class="skeleton-line"></div>
                  <div class="skeleton-line long"></div>
                  <div class="skeleton-line short"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <p>{{ error }}</p>
            <button @click="fetchNews" class="retry-btn">Coba Lagi</button>
          </div>

          <!-- News Grid/List -->
          <div v-else>
            <!-- Results Info -->
            <div class="results-info" v-if="news.length > 0">
              <p>Menampilkan {{ news.length }} berita</p>
              <div class="sort-options">
                <select class="sort-select">
                  <option value="latest">Terbaru</option>
                  <option value="popular">Terpopuler</option>
                  <option value="trending">Trending</option>
                </select>
              </div>
            </div>

            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="news-grid">
              <article class="news-card" v-for="item in news" :key="item.id">
                <div class="card-image">
                  <img 
                    :src="getFullImageUrl(item.featured_image)" 
                    :alt="item.title"
                    loading="lazy"
                    @error="handleImageError"
                  >
                  <div class="card-overlay">
                    <router-link :to="`/news/${item.slug}`" class="read-more-overlay">Baca Artikel</router-link>
                  </div>
                </div>
                <div class="card-content">
                  <div class="card-meta">
                    <span class="category-tag" :class="getCategoryClass(item.categories)">
                      {{ item.categories && item.categories.length > 0 ? item.categories[0].name : 'Umum' }}
                    </span>
                    <span class="publish-date">{{ formatDate(item.published_at || item.created_at) }}</span>
                  </div>
                  <h3 class="card-title">
                    <router-link :to="`/news/${item.slug}`">{{ item.title }}</router-link>
                  </h3>
                  <p class="card-excerpt">{{ item.excerpt || (item.content ? item.content.substring(0, 120) + '...' : '') }}</p>
                  <div class="card-footer">
                    <router-link :to="`/news/${item.slug}`" class="read-more">
                      Baca Selengkapnya
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                      </svg>
                    </router-link>
                    <div class="card-stats">
                      <span class="views">{{ formatViews(item.views || 0) }} views</span>
                      <span class="read-time">{{ item.read_time || 3 }} min baca</span>
                    </div>
                  </div>
                </div>
              </article>
            </div>

            <!-- List View -->
            <div v-else class="news-list">
              <article class="news-list-item" v-for="item in news" :key="item.id">
                <div class="list-image">
                  <img 
                    :src="getFullImageUrl(item.featured_image)" 
                    :alt="item.title"
                    loading="lazy"
                    @error="handleImageError"
                  >
                </div>
                <div class="list-content">
                  <div class="list-meta">
                    <span class="category-tag" :class="getCategoryClass(item.categories)">
                      {{ item.categories && item.categories.length > 0 ? item.categories[0].name : 'Umum' }}
                    </span>
                    <span class="publish-date">{{ formatDate(item.published_at || item.created_at) }}</span>
                  </div>
                  <h3 class="list-title">
                    <router-link :to="`/news/${item.slug}`">{{ item.title }}</router-link>
                  </h3>
                  <p class="list-excerpt">{{ item.excerpt || (item.content ? item.content.substring(0, 150) + '...' : '') }}</p>
                  <div class="list-footer">
                    <router-link :to="`/news/${item.slug}`" class="read-more">Baca Selengkapnya →</router-link>
                    <div class="list-stats">
                      <span class="views">{{ formatViews(item.views || 0) }} views</span>
                      <span class="read-time">{{ item.read_time || 3 }} min baca</span>
                    </div>
                  </div>
                </div>
              </article>
            </div>

            <!-- Empty State -->
            <div v-if="news.length === 0" class="empty-state">
              <div class="empty-icon">📰</div>
              <h3>Tidak Ada Berita Ditemukan</h3>
              <p>Coba ubah filter atau kata kunci pencarian Anda</p>
              <button @click="resetFilters" class="reset-filters-btn">Reset Filter</button>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper" v-if="lastPage > 1">
              <nav class="pagination">
                <button class="page-btn prev" @click="prevPage" :disabled="currentPage === 1">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 18l-6-6 6-6"/>
                  </svg>
                  Sebelumnya
                </button>

                <div class="page-numbers">
                  <button
                    class="page-number"
                    :class="{ active: currentPage === page }"
                    v-for="page in visiblePages"
                    :key="page"
                    @click="changePage(page)"
                    v-if="page !== '...'"
                  >
                    {{ page }}
                  </button>
                  <span v-else class="page-dots">...</span>
                </div>

                <button class="page-btn next" @click="nextPage" :disabled="currentPage === lastPage">
                  Selanjutnya
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                  </svg>
                </button>
              </nav>

              <div class="pagination-info">
                <p>Halaman {{ currentPage }} dari {{ lastPage }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <aside class="news-sidebar">
          <!-- Trending Section -->
          <div class="sidebar-widget trending-widget">
            <div class="widget-header">
              <h3 class="widget-title">Sedang Trending</h3>
              <div class="trending-icon">🔥</div>
            </div>
            <div class="trending-list">
              <div class="trending-item" v-for="(item, index) in getTrendingNews" :key="'trending-' + item.id">
                <div class="trending-rank-container">
                  <span class="trending-rank" :class="getRankClass(index + 1)">{{ index + 1 }}</span>
                </div>
                <div class="trending-content">
                  <h4 class="trending-title">
                    <router-link :to="`/news/${item.slug}`">{{ truncateText(item.title, 60) }}</router-link>
                  </h4>
                  <div class="trending-meta">
                    <span class="trending-views">{{ formatViews(item.views || Math.floor(Math.random() * 5000) + 1000) }} views</span>
                    <span class="trending-date">{{ formatDate(item.published_at || item.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Categories Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Kategori Populer</h3>
            <div class="category-list">
              <div class="category-item" v-for="category in popularCategories" :key="category.id" @click="selectCategory(category.slug)">
                <span class="category-name">{{ category.name }}</span>
                <span class="category-count">{{ category.news_count }}</span>
              </div>
            </div>
          </div>

          <!-- Newsletter Widget -->
          <div class="sidebar-widget newsletter-widget">
            <h3 class="widget-title">Berlangganan Newsletter</h3>
            <p>Dapatkan update berita terbaru langsung di email Anda</p>
            <form class="newsletter-form" @submit.prevent="subscribeNewsletter">
              <input 
                type="email" 
                placeholder="Email Anda" 
                class="newsletter-input"
                v-model="newsletterEmail"
                required
              >
              <button type="submit" class="newsletter-btn" :disabled="subscribing">
                {{ subscribing ? 'Mengirim...' : 'Berlangganan' }}
              </button>
            </form>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';
import config from '../config';

export default {
  name: 'News',
  data() {
    return {
      news: [],
      categories: [],
      selectedCategory: '',
      searchQuery: '',
      currentPage: 1,
      lastPage: 1,
      loading: false,
      error: null,
      viewMode: 'grid', // 'grid' or 'list'
      // Newsletter
      newsletterEmail: '',
      subscribing: false,
      // Popular categories with counts
      popularCategories: [
        { id: 1, name: 'Politik', slug: 'politik', news_count: 24 },
        { id: 2, name: 'Ekonomi', slug: 'ekonomi', news_count: 18 },
        { id: 3, name: 'Pendidikan', slug: 'pendidikan', news_count: 15 },
        { id: 4, name: 'Sosial', slug: 'sosial', news_count: 12 },
        { id: 5, name: 'Infrastruktur', slug: 'infrastruktur', news_count: 8 }
      ]
    }
  },
  computed: {
    visiblePages() {
      const pages = [];
      const current = this.currentPage;
      const last = this.lastPage;

      if (last <= 7) {
        for (let i = 1; i <= last; i++) {
          pages.push(i);
        }
      } else {
        if (current <= 4) {
          for (let i = 1; i <= 5; i++) pages.push(i);
          pages.push('...');
          pages.push(last);
        } else if (current >= last - 3) {
          pages.push(1);
          pages.push('...');
          for (let i = last - 4; i <= last; i++) pages.push(i);
        } else {
          pages.push(1);
          pages.push('...');
          for (let i = current - 1; i <= current + 1; i++) pages.push(i);
          pages.push('...');
          pages.push(last);
        }
      }

      return pages;
    },
    
    // Get top 5 trending news sorted by views
    getTrendingNews() {
      // Create a copy of news array and sort by views (descending)
      const sortedNews = [...this.news].sort((a, b) => {
        const viewsA = a.views || Math.floor(Math.random() * 5000) + 1000;
        const viewsB = b.views || Math.floor(Math.random() * 5000) + 1000;
        return viewsB - viewsA;
      });
      
      // Return top 5 or all if less than 5
      return sortedNews.slice(0, 5);
    }
  },
  async mounted() {
    // Fetch categories first
    await this.fetchCategories();
    // Then fetch news
    await this.fetchNews();
  },
  watch: {
    selectedCategory() {
      this.currentPage = 1;
      this.fetchNews();
    },
    searchQuery() {
      this.currentPage = 1;
      this.fetchNews();
    }
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await api.getCategories(true); // Use cache
        this.categories = response.data.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
        // Handle error appropriately, maybe set a default category list
        this.categories = [
          { id: 1, name: 'Politik', slug: 'politik' },
          { id: 2, name: 'Ekonomi', slug: 'ekonomi' },
          { id: 3, name: 'Olahraga', slug: 'olahraga' },
          { id: 4, name: 'Pendidikan', slug: 'pendidikan' },
          { id: 5, name: 'Sosial', slug: 'sosial' },
          { id: 6, name: 'Infrastruktur', slug: 'infrastruktur' }
        ];
      }
    },
    
    async fetchNews() {
      this.loading = true;
      this.error = null;

      try {
        const params = {
          page: this.currentPage,
          per_page: 12 // Limit results for better performance
        };

        if (this.selectedCategory) {
          params.category = this.selectedCategory;
        }

        if (this.searchQuery) {
          params.search = this.searchQuery;
        }

        const response = await api.getNews(params, true); // Use cache
        this.news = response.data.data;
        this.lastPage = response.data.pagination.last_page;
      } catch (error) {
        this.error = 'Gagal memuat berita. Silakan coba lagi.';
        console.error('Error fetching news:', error);
      } finally {
        this.loading = false;
      }
    },
    
    handleSearch() {
      this.currentPage = 1;
      this.fetchNews();
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.lastPage && page !== this.currentPage) {
        this.currentPage = page;
        this.fetchNews();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    
    nextPage() {
      if (this.currentPage < this.lastPage) {
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
      this.fetchNews();
    },
    
    selectCategory(slug) {
      this.selectedCategory = slug;
    },
    
    getCategoryClass(categories) {
      if (!categories || categories.length === 0) return 'category-default';
      const category = categories[0].name.toLowerCase();
      return `category-${category}`;
    },
    
    formatDate(dateString) {
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    
    formatViews(views) {
      if (views >= 1000000) {
        return (views / 1000000).toFixed(1) + 'M';
      } else if (views >= 1000) {
        return (views / 1000).toFixed(1) + 'k';
      }
      return views;
    },
    
    getFullImageUrl(imagePath) {
      if (!imagePath) return 'https://placehold.co/400x250?text=No+Image';
      return config.getStorageUrl(imagePath);
    },
    
    handleImageError(event) {
      event.target.src = 'https://placehold.co/400x250?text=Image+Not+Found';
    },
    
    async subscribeNewsletter() {
      if (!this.newsletterEmail) return;
      
      this.subscribing = true;
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        alert('Terima kasih! Anda telah berhasil berlangganan newsletter kami.');
        this.newsletterEmail = '';
      } catch (error) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
      } finally {
        this.subscribing = false;
      }
    },
    
    // Truncate text to specified length
    truncateText(text, length) {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    },
    
    // Get rank class for trending items
    getRankClass(rank) {
      switch (rank) {
        case 1:
          return 'first';
        case 2:
          return 'second';
        case 3:
          return 'third';
        default:
          return 'other';
      }
    }
  }
}
</script>

<style scoped>
.news-page {
  min-height: 100vh;
  background: #f8fffe;
}

/* Hero Section */
.news-hero {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 4rem 0 3rem;
  text-align: center;
}

.news-hero-content h1 {
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.news-hero-content p {
  font-size: 1.2rem;
  opacity: 0.95;
  margin: 0;
}

/* Container */
.news-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Filters */
.news-filters {
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

.search-btn:hover {
  background: #059669;
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

.filter-reset:hover {
  background: #e5e7eb;
}

.view-toggle {
  display: flex;
  background: #f3f4f6;
  border-radius: 10px;
  padding: 0.25rem;
  gap: 0.25rem;
}

.view-toggle button {
  background: transparent;
  border: none;
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.3s ease;
}

.view-toggle button.active {
  background: white;
  color: #10b981;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Content Layout */
.news-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 3rem;
  align-items: start;
}

/* Results Info */
.results-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.results-info p {
  margin: 0;
  color: #6b7280;
  font-weight: 500;
}

.sort-select {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9rem;
  background: white;
}

/* Skeleton Loaders */
.skeleton-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
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

.skeleton-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.skeleton-list-item {
  background: white;
  border-radius: 15px;
  padding: 1.5rem;
  display: flex;
  gap: 1.5rem;
  box-shadow: 0 5px 15px rgba(16, 185, 129, 0.08);
}

.skeleton-list-image {
  width: 150px;
  height: 100px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 10px;
  flex-shrink: 0;
}

.skeleton-list-content {
  flex: 1;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Grid View */
.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 2rem;
}

.news-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.news-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.card-image {
  position: relative;
  overflow: hidden;
  aspect-ratio: 16/9;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.card-overlay {
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

.news-card:hover .card-overlay {
  opacity: 1;
}

.news-card:hover .card-image img {
  transform: scale(1.05);
}

.read-more-overlay {
  color: white;
  background: rgba(255,255,255,0.2);
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  text-decoration: none;
  font-weight: 600;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
}

.card-content {
  padding: 1.5rem;
}

.card-meta {
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

.category-politik { background: #dbeafe; color: #1e40af; }
.category-ekonomi { background: #fef3c7; color: #d97706; }
.category-olahraga { background: #fce7f3; color: #be185d; }
.category-pendidikan { background: #dcfce7; color: #166534; }
.category-sosial { background: #e0e7ff; color: #4338ca; }
.category-infrastruktur { background: #fdf2f8; color: #be185d; }
.category-default { background: #f3f4f6; color: #4b5563; }

.publish-date {
  color: #9ca3af;
  font-size: 0.85rem;
}

.card-title {
  margin: 0 0 1rem;
  font-size: 1.2rem;
  line-height: 1.4;
}

.card-title a {
  color: #1f2937;
  text-decoration: none;
  font-weight: 600;
}

.card-title a:hover {
  color: #10b981;
}

.card-excerpt {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.read-more {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #10b981;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.read-more:hover {
  color: #047857;
  gap: 0.75rem;
}

.card-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #9ca3af;
}

/* List View */
.news-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.news-list-item {
  background: white;
  border-radius: 15px;
  padding: 1.5rem;
  display: flex;
  gap: 1.5rem;
  box-shadow: 0 5px 15px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
  transition: all 0.3s ease;
}

.news-list-item:hover {
  transform: translateX(5px);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.12);
}

.list-image {
  flex-shrink: 0;
}

.list-image img {
  width: 150px;
  height: 100px;
  object-fit: cover;
  border-radius: 10px;
}

.list-content {
  flex: 1;
}

.list-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.75rem;
}

.list-title {
  margin: 0 0 0.75rem;
  font-size: 1.1rem;
}

.list-title a {
  color: #1f2937;
  text-decoration: none;
  font-weight: 600;
}

.list-title a:hover {
  color: #10b981;
}

.list-excerpt {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.list-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.list-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #9ca3af;
}

/* States */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 5px solid #d1fae5;
  border-top: 5px solid #10b981;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1.5rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
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
.news-sidebar {
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

.widget-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.widget-title {
  margin: 0;
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

/* Trending Widget */
.trending-widget {
  position: relative;
  overflow: hidden;
}

.trending-icon {
  font-size: 1.5rem;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

/* Trending List */
.trending-list {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.trending-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding: 1rem 0;
  border-bottom: 1px solid #f3f4f6;
  transition: all 0.3s ease;
}

.trending-item:last-child {
  border-bottom: none;
}

.trending-item:hover {
  transform: translateX(5px);
}

.trending-rank-container {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.trending-rank {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.trending-rank.first {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: white;
  box-shadow: 0 4px 8px rgba(251, 191, 36, 0.3);
}

.trending-rank.second {
  background: linear-gradient(135deg, #d1d5db, #9ca3af);
  color: white;
  box-shadow: 0 4px 8px rgba(209, 213, 219, 0.3);
}

.trending-rank.third {
  background: linear-gradient(135deg, #cd7f32, #a85507);
  color: white;
  box-shadow: 0 4px 8px rgba(205, 127, 50, 0.3);
}

.trending-rank.other {
  background: #e5e7eb;
  color: #4b5563;
}

.trending-content {
  flex: 1;
}

.trending-title {
  margin: 0 0 0.5rem;
  font-size: 0.95rem;
  line-height: 1.4;
}

.trending-title a {
  color: #1f2937;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.trending-title a:hover {
  color: #10b981;
}

.trending-meta {
  display: flex;
  gap: 0.75rem;
  font-size: 0.75rem;
}

.trending-views {
  color: #10b981;
  font-weight: 600;
}

.trending-date {
  color: #9ca3af;
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
  transition: background 0.3s ease;
  cursor: pointer;
}

.category-item:hover {
  background: #f3f4f6;
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

/* Newsletter Widget */
.newsletter-widget {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.newsletter-widget .widget-title::after {
  background: rgba(255,255,255,0.3);
}

.newsletter-widget p {
  color: rgba(255,255,255,0.9);
  margin-bottom: 1.5rem;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.newsletter-input {
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 10px;
  font-size: 0.9rem;
}

.newsletter-btn {
  background: rgba(255,255,255,0.2);
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.3);
  transition: all 0.3s ease;
}

.newsletter-btn:hover:not(:disabled) {
  background: rgba(255,255,255,0.3);
}

.newsletter-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .news-content {
    grid-template-columns: 1fr 300px;
    gap: 2rem;
  }
}

@media (max-width: 992px) {
  .news-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .news-sidebar {
    order: -1;
  }

  .sidebar-widget {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .news-hero-content h1 {
    font-size: 2.5rem;
  }

  .news-container {
    padding: 0 1rem;
  }

  .news-filters {
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

  .news-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .skeleton-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .news-list-item {
    flex-direction: column;
    gap: 1rem;
  }

  .skeleton-list-item {
    flex-direction: column;
    gap: 1rem;
  }

  .list-image img {
    width: 100%;
    height: 200px;
  }

  .skeleton-list-image {
    width: 100%;
    height: 200px;
  }

  .pagination {
    flex-wrap: wrap;
    gap: 0.25rem;
  }

  .page-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  .news-hero-content h1 {
    font-size: 2rem;
  }

  .card-content {
    padding: 1rem;
  }

  .card-footer {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}
</style>