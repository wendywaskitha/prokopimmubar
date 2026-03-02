<template>
  <div class="latest-section">
    <h2 class="section-title">Berita Terbaru</h2>

    <div class="loading" v-if="loading">
      <div class="loading-spinner"></div>
      <p>Memuat berita...</p>
    </div>
    <div class="error" v-else-if="error">
      <i class="error-icon">⚠️</i>
      <p>{{ error }}</p>
    </div>
    <div class="latest-content" v-else>
      <div class="latest-main">
        <!-- Berita Utama -->
        <article class="featured-article" v-if="newsItems.length > 0" @click="goToNews(newsItems[0].slug)">
          <div class="featured-image">
            <img :src="getFullImageUrl(newsItems[0].featured_image)" :alt="newsItems[0].title" @error="handleImageError">
          </div>
          <div class="featured-content">
            <h3>{{ truncateText(newsItems[0].title, 80) }}</h3>
            <p>{{ truncateText(newsItems[0].excerpt || newsItems[0].content, 150) }}</p>
            <div class="article-meta">
              <span class="meta-item">Berita</span>
              <span class="meta-divider">•</span>
              <span class="meta-item">{{ newsItems[0].read_time || 3 }} menit baca</span>
            </div>
          </div>
        </article>

        <!-- Grid Berita Kecil -->
        <div class="news-grid">
          <article
            class="grid-article"
            v-for="news in newsItems.slice(1, 5)"
            :key="news.id"
            @click="goToNews(news.slug)"
          >
            <div class="grid-image">
              <img :src="getFullImageUrl(news.featured_image)" :alt="news.title" @error="handleImageError">
            </div>
            <div class="grid-content">
              <h4>{{ truncateText(news.title, 60) }}</h4>
              <p>{{ truncateText(news.excerpt || news.content, 100) }}</p>
              <div class="article-meta">
                <span class="meta-item">Berita</span>
                <span class="meta-divider">•</span>
                <span class="meta-item">{{ news.read_time || 3 }} menit baca</span>
              </div>
            </div>
          </article>
        </div>

        <router-link to="/news" class="load-more-btn">Muat Lebih Banyak</router-link>
      </div>

      <TrendingNews :news-items="newsItems" />
    </div>
  </div>
</template>

<script>
import api from '../services/api';
import TrendingNews from './TrendingNews.vue';
import config from '../config';

export default {
  name: 'LatestNews',
  components: {
    TrendingNews
  },
  data() {
    return {
      newsItems: [],
      loading: false,
      error: null
    }
  },
  mounted() {
    this.fetchNews();
  },
  methods: {
    async fetchNews() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.getNews({ per_page: 6 });
        this.newsItems = response.data.data;
      } catch (err) {
        this.error = 'Gagal memuat berita. Silakan coba lagi.';
        console.error('Error fetching news:', err);
      } finally {
        this.loading = false;
      }
    },
    getFullImageUrl(imagePath) {
      return config.getStorageUrl(imagePath) || 'https://placehold.co/600x400?text=No+Image';
    },
    goToNews(slug) {
      this.$router.push(`/news/${slug}`);
    },
    handleImageError(event) {
      event.target.src = 'https://placehold.co/600x400?text=Image+Not+Found';
    },
    truncateText(text, length) {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    }
  }
}
</script>

<style scoped>
.latest-section {
  max-width: 1200px;
  margin: 0 auto 4rem;
  padding: 0 2rem;
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 2rem;
  position: relative;
  padding-bottom: 0.75rem;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, #10b981, #059669);
  border-radius: 2px;
}

.latest-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 4rem;
}

.latest-main {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Featured Article Styles */
.featured-article {
  display: flex;
  flex-direction: column;
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
  margin-bottom: 2rem;
}

.featured-article:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  border-color: rgba(0, 0, 0, 0.1);
}

.featured-image {
  width: 100%;
  height: 300px;
  overflow: hidden;
  position: relative;
}

.featured-image::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60%;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
}

.featured-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.featured-article:hover .featured-image img {
  transform: scale(1.05);
}

.featured-content {
  padding: 1.5rem;
  position: relative;
  z-index: 1;
}

.featured-content h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
  line-height: 1.3;
}

.featured-content p {
  color: #6b7280;
  margin: 0 0 1rem;
  line-height: 1.6;
  font-size: 1rem;
}

/* News Grid Styles */
.news-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.grid-article {
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.03);
}

.grid-article:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
  border-color: rgba(0, 0, 0, 0.08);
}

.grid-image {
  width: 100%;
  height: 160px;
  overflow: hidden;
  position: relative;
}

.grid-image::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 40%;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.6));
}

.grid-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.grid-article:hover .grid-image img {
  transform: scale(1.05);
}

.grid-content {
  padding: 1rem;
}

.grid-content h4 {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.grid-content p {
  color: #6b7280;
  margin: 0 0 0.75rem;
  line-height: 1.5;
  font-size: 0.9rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.article-meta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #9ca3af;
}

.meta-item {
  color: #10b981;
  font-weight: 500;
}

.load-more-btn {
  align-self: flex-start;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 0.85rem 1.75rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.load-more-btn:hover {
  background: linear-gradient(135deg, #059669, #047857);
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3);
}

/* Loading & Error States */
.loading,
.error {
  text-align: center;
  padding: 3rem 2rem;
  border-radius: 15px;
  margin: 2rem 0;
}

.loading {
  background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
  color: #047857;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #d1fae5;
  border-top: 4px solid #10b981;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error {
  background: linear-gradient(135deg, #fef2f2, #fee2e2);
  color: #dc2626;
}

.error-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 1rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .latest-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .section-title {
    font-size: 1.75rem;
  }
  
  .featured-image {
    height: 250px;
  }
  
  .featured-content h3 {
    font-size: 1.4rem;
  }
}

@media (max-width: 768px) {
  .latest-section {
    padding: 0 1.5rem;
    margin-bottom: 3rem;
  }
  
  .section-title {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
  }
  
  .news-grid {
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
  }
  
  .featured-image {
    height: 220px;
  }
  
  .featured-content {
    padding: 1.25rem;
  }
  
  .featured-content h3 {
    font-size: 1.3rem;
  }
  
  .grid-content h4 {
    font-size: 1rem;
  }
  
  .grid-image {
    height: 140px;
  }
  
  .load-more-btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.95rem;
    align-self: center;
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .latest-section {
    padding: 0 1rem;
    margin-bottom: 2.5rem;
  }
  
  .section-title {
    font-size: 1.4rem;
    margin-bottom: 1.25rem;
  }
  
  .news-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-bottom: 1.25rem;
  }
  
  .featured-image {
    height: 200px;
  }
  
  .featured-content {
    padding: 1rem;
  }
  
  .featured-content h3 {
    font-size: 1.2rem;
  }
  
  .featured-content p {
    font-size: 0.9rem;
  }
  
  .grid-image {
    height: 160px;
  }
  
  .grid-content {
    padding: 0.85rem;
  }
  
  .grid-content h4 {
    font-size: 1rem;
  }
  
  .grid-content p {
    font-size: 0.85rem;
  }
  
  .article-meta {
    font-size: 0.75rem;
  }
  
  .load-more-btn {
    padding: 0.7rem 1.25rem;
    font-size: 0.9rem;
  }
}

@media (max-width: 400px) {
  .latest-section {
    padding: 0 0.75rem;
    margin-bottom: 2rem;
  }
  
  .section-title {
    font-size: 1.3rem;
    margin-bottom: 1rem;
  }
  
  .featured-image {
    height: 180px;
  }
  
  .featured-content {
    padding: 0.85rem;
  }
  
  .featured-content h3 {
    font-size: 1.1rem;
  }
  
  .featured-content p {
    font-size: 0.85rem;
  }
  
  .grid-image {
    height: 140px;
  }
  
  .grid-content {
    padding: 0.75rem;
  }
  
  .grid-content h4 {
    font-size: 0.95rem;
  }
  
  .grid-content p {
    font-size: 0.8rem;
  }
}
</style>
