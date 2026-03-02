<template>
  <div class="search-results-page">
    <div class="search-results-container">
      <div class="search-header">
        <h1>Hasil Pencarian</h1>
        <div class="search-info">
          <p>Ditemukan {{ totalResults }} hasil untuk kata kunci: "{{ searchTerm }}"</p>
        </div>
      </div>

      <!-- Search Form -->
      <div class="search-form-container">
        <form @submit.prevent="performSearch" class="search-form">
          <div class="search-input-group">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Cari berita, artikel, atau konten lainnya..."
              class="search-input"
            >
            <button type="submit" class="search-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </button>
          </div>
        </form>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <div class="loading-spinner"></div>
        <p>Mencari hasil...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <div class="error-icon">⚠️</div>
        <p>{{ error }}</p>
        <button @click="performSearch" class="retry-btn">Coba Lagi</button>
      </div>

      <!-- Search Results -->
      <div v-else>
        <div class="results-container">
          <!-- News Results -->
          <section v-if="newsResults.length > 0" class="results-section">
            <h2>Berita <span>({{ newsResults.length }})</span></h2>
            <div class="news-grid">
              <article class="news-card" v-for="item in newsResults" :key="item.id">
                <div class="card-image">
                  <img :src="item.featured_image ? config.getStorageUrl(item.featured_image) : 'https://placehold.co/400x250'" :alt="item.title">
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
                  </div>
                </div>
              </article>
            </div>
          </section>

          <!-- Gallery Results -->
          <section v-if="galleryResults.length > 0" class="results-section">
            <h2>Galeri <span>({{ galleryResults.length }})</span></h2>
            <div class="gallery-grid">
              <div class="gallery-item" v-for="item in galleryResults" :key="item.id">
                <router-link :to="`/gallery/${item.id}`" class="gallery-link">
                  <div class="gallery-image">
                    <img :src="item.images && item.images.length > 0 ? item.images[0].image_path : 'https://placehold.co/300x200'" :alt="item.title">
                  </div>
                  <div class="gallery-content">
                    <h3>{{ item.title }}</h3>
                    <p>{{ item.description }}</p>
                  </div>
                </router-link>
              </div>
            </div>
          </section>

          <!-- No Results -->
          <div v-if="totalResults === 0" class="no-results">
            <div class="no-results-icon">🔍</div>
            <h3>Tidak Ada Hasil Ditemukan</h3>
            <p>Coba gunakan kata kunci yang berbeda atau periksa ejaan Anda.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';
import config from '../config';

export default {
  name: 'SearchResults',
  data() {
    return {
      searchQuery: '',
      searchTerm: '',
      newsResults: [],
      galleryResults: [],
      loading: false,
      error: null,
      totalResults: 0
    }
  },
  mounted() {
    this.searchQuery = this.$route.query.q || '';
    this.searchTerm = this.searchQuery;
    if (this.searchQuery) {
      this.performSearch();
    }
  },
  methods: {
    async performSearch() {
      if (!this.searchQuery.trim()) return;
      
      this.loading = true;
      this.error = null;
      this.searchTerm = this.searchQuery;
      
      try {
        // Search news
        const newsResponse = await api.getNews({ search: this.searchQuery });
        this.newsResults = newsResponse.data.data;
        
        // Search galleries
        const galleryResponse = await api.getGalleries({ search: this.searchQuery });
        this.galleryResults = galleryResponse.data.data;
        
        // Calculate total results
        this.totalResults = this.newsResults.length + this.galleryResults.length;
      } catch (error) {
        this.error = 'Gagal melakukan pencarian. Silakan coba lagi.';
        console.error('Search error:', error);
      } finally {
        this.loading = false;
      }
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
    }
  },
  watch: {
    '$route'(to, from) {
      if (to.query.q !== from.query.q) {
        this.searchQuery = to.query.q || '';
        this.searchTerm = this.searchQuery;
        if (this.searchQuery) {
          this.performSearch();
        }
      }
    }
  }
}
</script>

<style scoped>
.search-results-page {
  min-height: 100vh;
  background: #f8fffe;
  padding: 2rem 0;
}

.search-results-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.search-header {
  text-align: center;
  margin-bottom: 2rem;
}

.search-header h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1f2937;
  margin-bottom: 1rem;
}

.search-info p {
  color: #6b7280;
  font-size: 1.1rem;
}

.search-form-container {
  max-width: 600px;
  margin: 0 auto 3rem;
}

.search-form {
  width: 100%;
}

.search-input-group {
  display: flex;
  position: relative;
}

.search-input {
  flex: 1;
  padding: 1rem 3.5rem 1rem 1.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-btn {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: #10b981;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.75rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-btn:hover {
  background: #059669;
}

.results-container {
  margin-top: 2rem;
}

.results-section {
  margin-bottom: 3rem;
}

.results-section h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.results-section h2 span {
  font-size: 1rem;
  background: #10b981;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-weight: 500;
}

/* News Grid */
.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
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
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.card-image {
  height: 200px;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.news-card:hover .card-image img {
  transform: scale(1.05);
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

/* Gallery Grid */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.gallery-item {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.gallery-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
}

.gallery-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.gallery-image {
  height: 200px;
  overflow: hidden;
}

.gallery-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.gallery-item:hover .gallery-image img {
  transform: scale(1.05);
}

.gallery-content {
  padding: 1.5rem;
}

.gallery-content h3 {
  margin: 0 0 0.75rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
}

.gallery-content p {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
  font-size: 0.9rem;
}

/* States */
.loading-state,
.error-state,
.no-results {
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

.error-state p,
.no-results p {
  color: #6b7280;
  margin: 1rem 0;
}

.error-icon,
.no-results-icon {
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

/* Responsive Design */
@media (max-width: 768px) {
  .search-results-container {
    padding: 0 1rem;
  }
  
  .search-header h1 {
    font-size: 2rem;
  }
  
  .news-grid,
  .gallery-grid {
    grid-template-columns: 1fr;
  }
  
  .news-card,
  .gallery-item {
    margin-bottom: 1.5rem;
  }
}

@media (max-width: 480px) {
  .card-content {
    padding: 1rem;
  }
  
  .card-title {
    font-size: 1.1rem;
  }
}
</style>