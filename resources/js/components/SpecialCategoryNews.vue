<template>
  <section class="special-category-news">
    <div class="container">
      <div class="section-header">
        <div class="header-content">
          <div class="header-text">
            <h2 class="section-title">Berita Strategis</h2>
            <p class="section-subtitle">Berita terkini seputar pertanian, peternakan, perikanan, dan pariwisata</p>
          </div>
          <a href="#" class="view-all-link">Lihat semua</a>
        </div>
      </div>

      <div class="category-tabs">
        <div class="tab-list">
          <button
            v-for="category in categories"
            :key="category.slug"
            :class="['tab-button', { active: activeCategory === category.slug }]"
            @click="setActiveCategory(category.slug)"
          >
            {{ category.name }}
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-container">
          <div class="spinner"></div>
          <p>Memuat berita...</p>
        </div>
      </div>

      <div v-else-if="error" class="error-state">
        <div class="error-content">
          <i class="fas fa-exclamation-circle error-icon"></i>
          <p>{{ error }}</p>
          <button @click="loadNews" class="retry-btn">Coba Lagi</button>
        </div>
      </div>

      <div v-else-if="news.length === 0" class="empty-state">
        <div class="empty-content">
          <i class="fas fa-newspaper empty-icon"></i>
          <p>Tidak ada berita dalam kategori {{ getActiveCategoryName() }}.</p>
          <p class="small">Berita untuk kategori ini akan ditampilkan saat tersedia.</p>
        </div>
      </div>

      <div v-else class="news-content">
        <!-- Featured News (Large Card) -->
        <div class="featured-news">
          <div class="news-card featured-card">
            <div class="card-image">
              <img
                :src="getFullImageUrl(featuredNews.featured_image)"
                :alt="featuredNews.title"
                loading="lazy"
                @error="handleImageError"
              >
              <div class="card-overlay">
                <router-link :to="`/news/${featuredNews.slug}`" class="read-more-overlay">
                  Baca Artikel
                </router-link>
              </div>
            </div>
            <div class="card-content">
              <div class="card-meta">
                <span class="category-tag" :class="getCategoryClass(featuredNews.categories)">
                  {{ getPrimaryCategory(featuredNews.categories) }}
                </span>
                <span class="publish-date">{{ formatDate(featuredNews.published_at || featuredNews.created_at) }}</span>
              </div>
              <h3 class="card-title">
                <router-link :to="`/news/${featuredNews.slug}`">{{ featuredNews.title }}</router-link>
              </h3>
              <p class="card-excerpt">{{ truncateText(featuredNews.excerpt || featuredNews.content, 150) }}</p>
              <div class="card-footer">
                <router-link :to="`/news/${featuredNews.slug}`" class="read-more">
                  Baca Selengkapnya
                  <i class="fas fa-arrow-right"></i>
                </router-link>
                <div class="card-stats">
                  <span class="views">{{ formatViews(featuredNews.views || 0) }} views</span>
                  <span class="read-time">{{ featuredNews.read_time || 3 }} min baca</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Other News (Grid) -->
        <div class="other-news">
          <div class="news-grid">
            <article
              class="news-card"
              v-for="item in otherNews"
              :key="item.id"
            >
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
                    {{ getPrimaryCategory(item.categories) }}
                  </span>
                  <span class="publish-date">{{ formatDate(item.published_at || item.created_at) }}</span>
                </div>
                <h3 class="card-title">
                  <router-link :to="`/news/${item.slug}`">{{ truncateText(item.title, 60) }}</router-link>
                </h3>
                <p class="card-excerpt">{{ truncateText(item.excerpt || item.content, 80) }}</p>
                <div class="card-footer">
                  <router-link :to="`/news/${item.slug}`" class="read-more">
                    Baca Selengkapnya
                    <i class="fas fa-arrow-right"></i>
                  </router-link>
                  <div class="card-stats">
                    <span class="views">{{ formatViews(item.views || 0) }} views</span>
                    <span class="read-time">{{ item.read_time || 3 }} min baca</span>
                  </div>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import api from '../services/api';

export default {
  name: 'SpecialCategoryNews',
  data() {
    return {
      categories: [
        { name: 'Pertanian', slug: 'pertanian' },
        { name: 'Peternakan', slug: 'peternakan' },
        { name: 'Perikanan', slug: 'perikanan' },
        { name: 'Pariwisata', slug: 'pariwisata' }
      ],
      activeCategory: 'pertanian',
      news: [],
      loading: false,
      error: null
    };
  },
  computed: {
    featuredNews() {
      return this.news.length > 0 ? this.news[0] : null;
    },
    otherNews() {
      return this.news.length > 1 ? this.news.slice(1, 4) : []; // Batasi hanya 3 item
    }
  },
  async mounted() {
    await this.loadNews();
  },
  methods: {
    async loadNews() {
      this.loading = true;
      this.error = null;

      try {
        console.log('Loading news for category:', this.activeCategory);
        const response = await api.getNews({
          category: this.activeCategory,
          per_page: 4 // Ubah ke 4 item total (1 featured + 3 other)
        }, true);

        console.log('API Response:', response);
        this.news = response.data.data || [];
        console.log('News loaded:', this.news);
      } catch (err) {
        this.error = 'Gagal memuat berita. Silakan coba lagi.';
        console.error('Error loading special category news:', err);
      } finally {
        this.loading = false;
      }
    },

    setActiveCategory(categorySlug) {
      if (this.activeCategory !== categorySlug) {
        this.activeCategory = categorySlug;
        this.loadNews();
      }
    },

    getCategoryClass(categories) {
      if (!Array.isArray(categories) || categories.length === 0) {
        return 'category-default';
      }

      const category = categories[0];
      if (!category || !category.name) {
        return 'category-default';
      }

      const categoryName = category.name.toLowerCase();
      return `category-${categoryName}`;
    },

    getPrimaryCategory(categories) {
      if (!Array.isArray(categories) || categories.length === 0) {
        return 'Umum';
      }

      const category = categories[0];
      if (!category || !category.name) {
        return 'Umum';
      }

      return category.name;
    },

    getActiveCategoryName() {
      const category = this.categories.find(cat => cat.slug === this.activeCategory);
      return category ? category.name : this.activeCategory;
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

    truncateText(text, length) {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    },

    getFullImageUrl(imagePath) {
      if (!imagePath) return 'https://placehold.co/600x400?text=No+Image';
      if (imagePath.startsWith('http')) return imagePath;
      return `http://localhost:8000/storage/${imagePath}`;
    },

    handleImageError(event) {
      event.target.src = 'https://placehold.co/600x400?text=Image+Not+Found';
    }
  }
};
</script>

<style scoped>
.special-category-news {
  padding: 4rem 0;
  background-color: #f8fafc;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.section-header {
  margin-bottom: 2.5rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.header-text {
  flex: 1;
}

.section-title {
  font-size: 2rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 0.5rem;
  position: relative;
  display: inline-block;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 4px;
  background-color: #10b981;
  border-radius: 2px;
}

.section-subtitle {
  font-size: 1rem;
  color: #64748b;
  margin: 0;
}

.view-all-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 0.5rem 0;
  border-bottom: 2px solid transparent;
  transition: border-color 0.3s ease;
}

.view-all-link:hover {
  border-bottom-color: #10b981;
}

.category-tabs {
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
}

.tab-list {
  display: flex;
  gap: 0.5rem;
  padding: 0.5rem;
  background-color: #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.tab-button {
  padding: 0.6rem 1.5rem;
  border: none;
  border-radius: 12px;
  background: transparent;
  color: #64748b;
  font-weight: 500;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab-button:hover {
  color: #1e293b;
}

.tab-button.active {
  background-color: #10b981;
  color: white;
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

.spinner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-left-color: #10b981;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-content .small {
  font-size: 0.9rem;
  color: #94a3b8;
  margin-top: 0.5rem;
}

.error-icon, .empty-icon {
  font-size: 3rem;
  color: #cbd5e1;
}

.retry-btn {
  background-color: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  margin-top: 1rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.retry-btn:hover {
  background-color: #059669;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3);
}

.news-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

/* Featured News */
.featured-news {
  grid-column: 1;
}

.featured-card {
  height: 100%;
}

.featured-card .card-image {
  aspect-ratio: contains;
  height: 300px;
}

/* Other News */
.other-news {
  grid-column: 2;
}

.news-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  height: 100%;
}

.news-grid .news-card {
  display: flex;
  flex-direction: row;
  height: 120px;
  flex: 1;
}

.news-grid .card-image {
  width: 120px;
  height: 100%;
  flex-shrink: 0;
}

.news-grid .card-content {
  padding: 0.75rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.news-grid .card-meta {
  margin-bottom: 0.5rem;
}

.news-grid .card-title {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  flex: 1;
}

.news-grid .card-excerpt {
  font-size: 0.8rem;
  margin-bottom: 0.5rem;
  line-height: 1.4;
  flex: 1;
}

.news-grid .card-footer {
  display: none; /* Sembunyikan footer untuk menghemat ruang */
}

.news-grid .category-tag {
  font-size: 0.7rem;
  padding: 0.2rem 0.6rem;
}

.news-grid .publish-date {
  font-size: 0.75rem;
}

/* Card Styles */
.news-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.news-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  border-color: rgba(0, 0, 0, 0.1);
}

.card-image {
  position: relative;
  overflow: hidden;
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
  background: rgba(30, 41, 59, 0.8);
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
  background: rgba(255, 255, 255, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.85rem;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
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

.category-pertanian {
  background-color: #dcfce7;
  color: #166534;
}

.category-peternakan {
  background-color: #fef3c7;
  color: #d97706;
}

.category-perikanan {
  background-color: #dbeafe;
  color: #1e40af;
}

.category-pariwisata {
  background-color: #fce7f3;
  color: #be185d;
}

.category-default {
  background-color: #f3f4f6;
  color: #4b5563;
}

.publish-date {
  color: #9ca3af;
  font-size: 0.85rem;
}

.card-title {
  margin: 0 0 1rem;
  font-size: 1.25rem;
  line-height: 1.4;
}

.card-title a {
  color: #1f2937;
  text-decoration: none;
  font-weight: 700;
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

/* Responsive Design */
@media (max-width: 992px) {
  .news-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .featured-news, .other-news {
    grid-column: 1;
  }

  .news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  .news-grid .news-card {
    flex-direction: column;
    height: auto;
  }

  .news-grid .card-image {
    width: 100%;
    height: 150px;
  }

  .news-grid .card-footer {
    display: flex;
  }

  .header-content {
    flex-direction: column;
    gap: 1rem;
  }

  .special-category-news {
    padding: 3rem 0;
  }

  .section-title {
    font-size: 1.75rem;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }

  .tab-list {
    flex-wrap: wrap;
    justify-content: center;
  }

  .tab-button {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }

  .news-grid {
    grid-template-columns: 1fr;
  }

  .featured-card .card-image {
    height: 250px;
  }
}

@media (max-width: 576px) {
  .special-category-news {
    padding: 2rem 0;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .section-subtitle {
    font-size: 0.9rem;
  }

  .view-all-link {
    font-size: 0.9rem;
  }

  .tab-button {
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
  }

  .card-content {
    padding: 1rem;
  }

  .card-title {
    font-size: 1.1rem;
  }

  .card-excerpt {
    font-size: 0.85rem;
  }

  .featured-card .card-image {
    height: 200px;
  }

  .news-grid .card-image {
    height: 130px;
  }

  .category-tag {
    font-size: 0.7rem;
    padding: 0.2rem 0.6rem;
  }

  .publish-date {
    font-size: 0.75rem;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 0.75rem;
  }

  .header-content {
    text-align: center;
  }

  .section-title {
    font-size: 1.4rem;
  }

  .section-subtitle {
    font-size: 0.85rem;
  }

  .tab-list {
    padding: 0.4rem;
    border-radius: 12px;
  }

  .tab-button {
    padding: 0.35rem 0.7rem;
    font-size: 0.8rem;
  }

  .card-content {
    padding: 0.75rem;
  }

  .card-title {
    font-size: 1rem;
  }

  .card-excerpt {
    font-size: 0.8rem;
  }

  .card-footer {
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
  }

  .read-more {
    font-size: 0.85rem;
  }

  .card-stats {
    font-size: 0.75rem;
    gap: 0.75rem;
  }

  .featured-card .card-image {
    height: 180px;
  }

  .news-grid .card-image {
    height: 120px;
  }
}
</style>
