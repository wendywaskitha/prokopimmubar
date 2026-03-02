<template>
  <div class="news-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Memuat berita...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h2>{{ error }}</h2>
      <button @click="loadNews" class="retry-btn">Coba Lagi</button>
      <router-link to="/news" class="back-link">Kembali ke Berita</router-link>
    </div>

    <!-- News Content -->
    <div v-else-if="news" class="news-detail-container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <div class="breadcrumb-container">
          <router-link to="/">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            </svg>
            Beranda
          </router-link>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
          <router-link to="/news">Berita</router-link>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
          <span>{{ news.title }}</span>
        </div>
      </nav>

      <div class="news-content-layout">
        <!-- Main Content -->
        <main class="news-main-content">
          <article class="news-article">
            <!-- News Header -->
            <header class="news-header">
              <div class="news-categories">
                <span
                  v-for="category in news.categories"
                  :key="category.id"
                  class="category-tag"
                  :style="{ backgroundColor: getCategoryColor(category.name) }"
                >
                  {{ category.name }}
                </span>
              </div>

              <h1 class="news-title">{{ news.title }}</h1>

              <div class="news-meta">
                <div class="meta-group">
                  <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>{{ news.author ? news.author.name : 'Administrator' }}</span>
                  </div>

                  <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>{{ formatDate(news.published_at) }}</span>
                  </div>

                  <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>{{ news.views || Math.floor(Math.random() * 5000) + 1000 }} pembaca</span>
                  </div>

                  <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span>{{ Math.floor(Math.random() * 100) + 20 }} suka</span>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="news-actions">
                  <button class="action-btn bookmark" title="Simpan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                    </svg>
                  </button>
                  <button class="action-btn print" @click="printPage" title="Cetak">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="6,9 6,2 18,2 18,9"/>
                      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                      <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                  </button>
                  <button class="action-btn font-size" @click="toggleFontSize" title="Ukuran Font">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M4 7V4h16v3M9 20h6M12 4v16"/>
                    </svg>
                  </button>
                </div>
              </div>
            </header>

            <!-- Featured Image -->
            <div class="featured-image" v-if="news.featured_image">
              <img :src="getFullImageUrl(news.featured_image)" :alt="news.title">
              <div class="image-caption" v-if="news.featured_image_caption">
                {{ news.featured_image_caption }}
              </div>
            </div>

            <!-- Reading Progress -->
            <div class="reading-progress">
              <div class="progress-bar" :style="{ width: readingProgress + '%' }"></div>
            </div>

            <!-- News Content -->
            <div class="news-content" :class="{ 'large-font': largeFontEnabled }" v-html="formatContent(news.content)"></div>

            <!-- Share Section -->
            <div class="news-share">
              <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                  <polyline points="16,6 12,2 8,6"/>
                  <line x1="12" y1="2" x2="12" y2="15"/>
                </svg>
                Bagikan Berita
              </h3>
              <div class="share-buttons">
                <button class="share-btn facebook" @click="shareToFacebook">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                  <span>Facebook</span>
                </button>
                <button class="share-btn twitter" @click="shareToTwitter">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                  <span>Twitter</span>
                </button>
                <button class="share-btn whatsapp" @click="shareToWhatsApp">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                  </svg>
                  <span>WhatsApp</span>
                </button>
                <button class="share-btn telegram" @click="shareToTelegram">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                  </svg>
                  <span>Telegram</span>
                </button>
                <button class="share-btn copy" @click="copyUrl">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.72-1.71"/>
                  </svg>
                  <span>Salin Link</span>
                </button>
              </div>
            </div>

            <!-- News Tags -->
            <div class="news-tags" v-if="news.tags && news.tags.length > 0">
              <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                  <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                Tags
              </h3>
              <div class="tags-container">
                <router-link
                  v-for="tag in news.tags"
                  :key="tag.id"
                  :to="`/news?tag=${tag.slug}`"
                  class="tag"
                >
                  {{ tag.name }}
                </router-link>
              </div>
            </div>

            <!-- Gallery -->
            <div class="news-gallery" v-if="news.gallery && news.gallery.images && news.gallery.images.length > 0">
              <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                  <circle cx="8.5" cy="8.5" r="1.5"/>
                  <polyline points="21,15 16,10 5,21"/>
                </svg>
                Galeri Foto
              </h3>
              <div class="gallery-grid">
                <div
                  v-for="(image, index) in news.gallery.images"
                  :key="image.id"
                  class="gallery-item"
                  @click="openLightbox(index)"
                >
                  <img :src="getFullImageUrl(image.image_path)" :alt="image.caption || 'Gallery image'">
                  <div class="gallery-overlay">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </div>
                  <div class="gallery-caption" v-if="image.caption">
                    {{ image.caption }}
                  </div>
                </div>
              </div>
            </div>
          </article>

          <!-- Related News -->
          <section class="related-news" v-if="relatedNews.length > 0">
            <div class="section-header">
              <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14,2 14,8 20,8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Berita Terkait
              </h2>
              <p>Artikel lain yang mungkin menarik untuk Anda</p>
            </div>
            <div class="related-news-grid">
              <article
                v-for="related in relatedNews"
                :key="related.id"
                class="related-news-item"
              >
                <router-link :to="`/news/${related.slug}`" class="related-news-link">
                  <div class="related-image" v-if="related.featured_image">
                    <img :src="getFullImageUrl(related.featured_image)" :alt="related.title">
                    <div class="related-overlay">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                      </svg>
                    </div>
                  </div>
                  <div class="related-content">
                    <div class="related-category" v-if="related.categories && related.categories.length > 0">
                      {{ related.categories[0].name }}
                    </div>
                    <h3>{{ related.title }}</h3>
                    <div class="related-meta">
                      <span>{{ formatDate(related.published_at) }}</span>
                      <span class="read-time">3 min baca</span>
                    </div>
                  </div>
                </router-link>
              </article>
            </div>
          </section>
        </main>

        <!-- Sidebar -->
        <aside class="news-detail-sidebar">
          <!-- Popular News Widget -->
          <div class="sidebar-widget" v-if="popularNews.length > 0">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12,2 15.09,8.26 22,9 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9 8.91,8.26"/>
              </svg>
              Berita Populer
            </h3>
            <div class="popular-news-list">
              <article class="popular-news-item" v-for="(item, index) in popularNews" :key="item.id">
                <div class="popular-news-rank">{{ index + 1 }}</div>
                <div class="popular-news-content">
                  <h4>
                    <router-link :to="`/news/${item.slug}`">{{ item.title }}</router-link>
                  </h4>
                  <div class="popular-news-meta">
                    <span>{{ formatDate(item.published_at) }}</span>
                    <span class="views">{{ item.views || Math.floor(Math.random() * 5000) + 1000 }} views</span>
                  </div>
                </div>
              </article>
            </div>
          </div>
          <!-- Fallback message when no popular news -->
          <div class="sidebar-widget" v-else>
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12,2 15.09,8.26 22,9 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9 8.91,8.26"/>
              </svg>
              Berita Populer
            </h3>
            <p class="no-content-message">Berita populer tidak tersedia saat ini.</p>
          </div>

          <!-- Categories Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
              </svg>
              Kategori Berita
            </h3>
            <div class="categories-list">
              <div
                v-for="category in categories"
                :key="category.id"
                class="category-item"
                :class="{ active: news.categories && news.categories.some(c => c.id === category.id) }"
              >
                <router-link :to="`/news?category=${category.slug}`">
                  <span class="category-name">{{ category.name }}</span>
                  <span class="category-count">{{ category.news_count }}</span>
                </router-link>
              </div>
            </div>
          </div>

          <!-- Newsletter Widget -->
          <div class="sidebar-widget newsletter-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
              Berlangganan Newsletter
            </h3>
            <p>Dapatkan berita terbaru langsung di email Anda</p>
            <form class="newsletter-form" @submit.prevent="subscribeNewsletter">
              <input type="email" placeholder="Email Anda" v-model="newsletterEmail" required>
              <button type="submit" :disabled="subscribing">
                <span v-if="subscribing">
                  <div class="loading-spinner small"></div>
                  Berlangganan...
                </span>
                <span v-else>Berlangganan</span>
              </button>
            </form>
          </div>

          <!-- Tags Widget -->
          <div class="sidebar-widget" v-if="allTags.length > 0">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                <line x1="7" y1="7" x2="7.01" y2="7"/>
              </svg>
              Tags Populer
            </h3>
            <div class="tags-cloud">
              <router-link
                v-for="tag in allTags"
                :key="tag.id"
                :to="`/news?tag=${tag.slug}`"
                class="tag-cloud-item"
              >
                {{ tag.name }}
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
  name: 'NewsDetail',
  data() {
    return {
      news: null,
      relatedNews: [],
      popularNews: [],
      categories: [
        { id: 1, name: 'Pemerintahan', slug: 'pemerintahan', news_count: 24 },
        { id: 2, name: 'Pembangunan', slug: 'pembangunan', news_count: 18 },
        { id: 3, name: 'Kesehatan', slug: 'kesehatan', news_count: 15 },
        { id: 4, name: 'Pendidikan', slug: 'pendidikan', news_count: 12 },
        { id: 5, name: 'Wisata', slug: 'wisata', news_count: 8 },
        { id: 6, name: 'Olahraga', slug: 'olahraga', news_count: 6 },
        { id: 7, name: 'Kebudayaan', slug: 'kebudayaan', news_count: 10 }
      ],
      allTags: [
        { id: 1, name: 'Berita', slug: 'berita' },
        { id: 2, name: 'Kabupaten', slug: 'kabupaten' },
        { id: 3, name: 'Muna Barat', slug: 'muna-barat' },
        { id: 4, name: 'Pemerintah', slug: 'pemerintah' },
        { id: 5, name: 'Pembangunan', slug: 'pembangunan' },
        { id: 6, name: 'Infrastruktur', slug: 'infrastruktur' },
        { id: 7, name: 'Pendidikan', slug: 'pendidikan' },
        { id: 8, name: 'Kesehatan', slug: 'kesehatan' }
      ],
      loading: true,
      error: null,
      readingProgress: 0,
      largeFontEnabled: false,
      newsletterEmail: '',
      subscribing: false,
      categoryColors: {
        'Berita Umum': '#10b981',
        'Pemerintahan': '#3b82f6',
        'Pembangunan': '#f59e0b',
        'Kesehatan': '#ef4444',
        'Pendidikan': '#8b5cf6',
        'Wisata': '#ec4899',
        'Olahraga': '#06b6d4',
        'Kebudayaan': '#f97316'
      }
    }
  },
  mounted() {
    this.loadNews();
    this.loadPopularNews();
    this.setupScrollListener();
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    getFullImageUrl(imagePath) {
      if (imagePath.startsWith('http')) {
        return imagePath;
      }
      const cleanPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath;
      return `/storage/${cleanPath}`;
    },

    async loadNews() {
      this.loading = true;
      this.error = null;

      try {
        const slug = this.$route.params.slug;
        const response = await api.getNewsBySlug(slug);

        if (response.data.success) {
          this.news = response.data.data;

          if (this.news.categories && this.news.categories.length > 0) {
            await this.loadRelatedNews(this.news.categories[0].slug);
          }
        } else {
          this.error = 'Berita tidak ditemukan';
        }
      } catch (error) {
        console.error('Error loading news:', error);
        if (error.response && error.response.status === 404) {
          this.error = 'Berita tidak ditemukan';
        } else {
          this.error = 'Terjadi kesalahan saat memuat berita';
        }
      } finally {
        this.loading = false;
      }
    },

    async loadRelatedNews(categorySlug) {
      try {
        const response = await api.getNewsByCategory(categorySlug);

        if (response.data.success) {
          this.relatedNews = response.data.data.filter(news => news.id !== this.news.id).slice(0, 3);
        }
      } catch (error) {
        console.error('Error loading related news:', error);
      }
    },

    async loadPopularNews() {
      try {
        const response = await api.getPopularNews();

        if (response.data.success) {
          this.popularNews = response.data.data.slice(0, 5);
        }
      } catch (error) {
        console.error('Error loading popular news:', error);
        // Fallback to regular news if popular news API fails
        try {
          const response = await api.getNews({ per_page: 5 });
          if (response.data.success) {
            this.popularNews = response.data.data;
          }
        } catch (fallbackError) {
          console.error('Error loading fallback news:', fallbackError);
          this.popularNews = [];
        }
      }
    },

    setupScrollListener() {
      window.addEventListener('scroll', this.handleScroll);
    },

    handleScroll() {
      const article = document.querySelector('.news-content');
      if (!article) return;

      const articleTop = article.offsetTop;
      const articleHeight = article.offsetHeight;
      const windowHeight = window.innerHeight;
      const scrollTop = window.scrollY;

      const progress = Math.min(
        100,
        Math.max(0, ((scrollTop + windowHeight - articleTop) / articleHeight) * 100)
      );

      this.readingProgress = progress;
    },

    getCategoryColor(categoryName) {
      return this.categoryColors[categoryName] || '#6b7280';
    },

    formatDate(dateString) {
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },

    formatContent(content) {
      if (!content) return '';

      return content
        .split('\n\n')
        .map(paragraph => `<p>${paragraph.replace(/\n/g, '<br>')}</p>`)
        .join('');
    },

    printPage() {
      window.print();
    },

    toggleFontSize() {
      this.largeFontEnabled = !this.largeFontEnabled;
    },

    openLightbox(index) {
      // Implement lightbox functionality
      console.log('Open lightbox for image', index);
    },

    shareToFacebook() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.news.title);
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&t=${title}`, '_blank');
    },

    shareToTwitter() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.news.title);
      window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank');
    },

    shareToWhatsApp() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.news.title);
      window.open(`https://wa.me/?text=${title}%20${url}`, '_blank');
    },

    shareToTelegram() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.news.title);
      window.open(`https://t.me/share/url?url=${url}&text=${title}`, '_blank');
    },

    async copyUrl() {
      try {
        await navigator.clipboard.writeText(window.location.href);
        // Show success message
        alert('Link berhasil disalin!');
      } catch (err) {
        console.error('Failed to copy URL:', err);
      }
    },

    async subscribeNewsletter() {
      if (!this.newsletterEmail) return;

      this.subscribing = true;
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000));
        alert('Berhasil berlangganan newsletter!');
        this.newsletterEmail = '';
      } catch (error) {
        alert('Gagal berlangganan. Silakan coba lagi.');
      } finally {
        this.subscribing = false;
      }
    }
  },
  watch: {
    '$route'(to, from) {
      if (to.params.slug !== from.params.slug) {
        this.loadNews();
      }
    }
  }
}
</script>

<style scoped>
.news-detail-page {
  min-height: 100vh;
  background: #f8fffe;
  padding: 2rem 0;
}

.news-detail-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Breadcrumb */
.breadcrumb {
  margin-bottom: 2rem;
}

.breadcrumb-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);
  font-size: 0.9rem;
}

.breadcrumb-container a {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #10b981;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.breadcrumb-container a:hover {
  color: #059669;
}

.breadcrumb-container svg {
  color: #9ca3af;
}

.breadcrumb-container span:last-child {
  color: #6b7280;
  font-weight: 500;
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Content Layout */
.news-content-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 3rem;
}

/* Main Content */
.news-main-content {
  order: 1;
}

/* News Article */
.news-article {
  background: white;
  border-radius: 24px;
  padding: 3rem;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.08);
  margin-bottom: 3rem;
  border: 1px solid rgba(16, 185, 129, 0.1);
  position: relative;
  overflow: hidden;
}

/* News Header */
.news-categories {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.category-tag {
  padding: 0.6rem 1.2rem;
  border-radius: 25px;
  color: white;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.news-title {
  font-size: 2.8rem;
  font-weight: 800;
  color: #1f2937;
  margin: 0 0 2rem;
  line-height: 1.1;
  letter-spacing: -0.02em;
}

.news-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 0;
  border-bottom: 2px solid #f3f4f6;
  margin-bottom: 2rem;
}

.meta-group {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.95rem;
  font-weight: 500;
}

.meta-item svg {
  color: #10b981;
}

.news-actions {
  display: flex;
  gap: 0.75rem;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn:hover {
  background: #10b981;
  border-color: #10b981;
  color: white;
  transform: translateY(-2px);
}

/* Featured Image */
.featured-image {
  margin: 2rem 0;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.featured-image img {
  width: 100%;
  height: auto;
  display: block;
}

.image-caption {
  padding: 1rem 1.5rem;
  background: #f9fafb;
  color: #6b7280;
  font-size: 0.9rem;
  font-style: italic;
  border-top: 1px solid #e5e7eb;
}

/* Reading Progress */
.reading-progress {
  position: sticky;
  top: 0;
  height: 4px;
  background: #e5e7eb;
  z-index: 100;
  margin: -3rem -3rem 2rem;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(135deg, #10b981, #059669);
  transition: width 0.3s ease;
}

/* News Content */
.news-content {
  margin: 2rem 0;
  color: #374151;
  line-height: 1.8;
  font-size: 1.1rem;
  transition: font-size 0.3s ease;
}

.news-content.large-font {
  font-size: 1.25rem;
  line-height: 1.9;
}

.news-content p {
  margin-bottom: 1.8rem;
}

.news-content h2,
.news-content h3,
.news-content h4 {
  color: #1f2937;
  margin: 2.5rem 0 1.5rem;
  font-weight: 700;
}

.news-content h2 {
  font-size: 1.8rem;
  border-bottom: 3px solid #10b981;
  padding-bottom: 0.5rem;
}

.news-content h3 {
  font-size: 1.5rem;
}

.news-content h4 {
  font-size: 1.3rem;
}

.news-content ul,
.news-content ol {
  margin: 1.8rem 0;
  padding-left: 2rem;
}

.news-content li {
  margin-bottom: 0.8rem;
}

.news-content blockquote {
  background: #f0fdf4;
  border-left: 4px solid #10b981;
  padding: 1.5rem;
  margin: 2rem 0;
  border-radius: 0 12px 12px 0;
  font-style: italic;
  color: #166534;
}

/* News Tags */
.news-tags {
  margin: 3rem 0;
  padding: 2rem;
  background: #f9fafb;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
}

.news-tags h3 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0 0 1.5rem;
  color: #1f2937;
  font-size: 1.2rem;
  font-weight: 700;
}

.news-tags h3 svg {
  color: #10b981;
}

.tags-container {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.tag {
  background: white;
  color: #10b981;
  padding: 0.6rem 1.2rem;
  border-radius: 25px;
  font-size: 0.85rem;
  font-weight: 600;
  text-decoration: none;
  border: 1px solid #10b981;
  transition: all 0.3s ease;
}

.tag:hover {
  background: #10b981;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* News Gallery */
.news-gallery {
  margin: 3rem 0;
}

.news-gallery h3 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0 0 2rem;
  color: #1f2937;
  font-size: 1.5rem;
  font-weight: 700;
}

.news-gallery h3 svg {
  color: #10b981;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.gallery-item {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.3s ease;
}

.gallery-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.gallery-item img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  display: block;
  transition: transform 0.3s ease;
}

.gallery-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(16, 185, 129, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
  opacity: 1;
}

.gallery-item:hover img {
  transform: scale(1.1);
}

.gallery-overlay svg {
  color: white;
}

.gallery-caption {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 1rem;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  color: white;
  font-size: 0.9rem;
  transform: translateY(100%);
  transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-caption {
  transform: translateY(0);
}

/* Share Section */
.news-share {
  margin: 3rem 0;
  padding: 2rem;
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
  border-radius: 20px;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.news-share h3 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0 0 1.5rem;
  color: #1f2937;
  font-size: 1.2rem;
  font-weight: 700;
}

.news-share h3 svg {
  color: #10b981;
}

.share-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.share-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  min-width: 130px;
  justify-content: center;
}

.share-btn.facebook {
  background: #1877f2;
  color: white;
}

.share-btn.twitter {
  background: #1da1f2;
  color: white;
}

.share-btn.whatsapp {
  background: #25d366;
  color: white;
}

.share-btn.telegram {
  background: #0088cc;
  color: white;
}

.share-btn.copy {
  background: #6b7280;
  color: white;
}

.share-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* Related News */
.related-news {
  background: white;
  border-radius: 24px;
  padding: 3rem;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-header h2 {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  font-size: 2rem;
  font-weight: 800;
  color: #1f2937;
  margin: 0 0 1rem;
}

.section-header h2 svg {
  color: #10b981;
}

.section-header p {
  color: #6b7280;
  font-size: 1.1rem;
  margin: 0;
}

.related-news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.related-news-item {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.related-news-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.related-news-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.related-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.related-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.related-overlay {
  position: absolute;
  top: 50%;
  right: 1.5rem;
  transform: translateY(-50%);
  background: rgba(16, 185, 129, 0.9);
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.3s ease;
}

.related-news-item:hover .related-overlay {
  opacity: 1;
  transform: translateY(-50%) translateX(-10px);
}

.related-news-item:hover .related-image img {
  transform: scale(1.1);
}

.related-content {
  padding: 1.5rem;
}

.related-category {
  background: #10b981;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
  margin-bottom: 1rem;
}

.related-content h3 {
  margin: 0 0 1rem;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1.4;
}

.related-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #9ca3af;
  font-size: 0.85rem;
  font-weight: 500;
}

.read-time {
  background: #f3f4f6;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  color: #6b7280;
}

/* Sidebar */
.news-detail-sidebar {
  order: 2;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.sidebar-widget {
  background: white;
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
  transition: all 0.3s ease;
}

.sidebar-widget:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 50px rgba(16, 185, 129, 0.12);
}

.widget-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.3rem;
  font-weight: 800;
  color: #1f2937;
  margin: 0 0 2rem;
  position: relative;
  padding-bottom: 1rem;
}

.widget-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 2px;
}

.widget-title svg {
  color: #10b981;
}

/* Popular News */
.popular-news-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.popular-news-item {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
  transition: all 0.3s ease;
}

.popular-news-item:hover {
  background: #f0fdf4;
  border-color: #10b981;
  transform: translateX(5px);
}

.popular-news-rank {
  font-size: 1.8rem;
  font-weight: 800;
  color: #10b981;
  min-width: 40px;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 12px;
  height: 40px;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}

.popular-news-content {
  flex: 1;
}

.popular-news-content h4 {
  margin: 0 0 0.75rem;
  font-size: 1rem;
  line-height: 1.4;
  font-weight: 600;
}

.popular-news-content h4 a {
  color: #1f2937;
  text-decoration: none;
  transition: color 0.3s ease;
}

.popular-news-content h4 a:hover {
  color: #10b981;
}

.popular-news-meta {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
  color: #9ca3af;
  font-weight: 500;
}

.views {
  background: #e5e7eb;
  padding: 0.25rem 0.5rem;
  border-radius: 8px;
  color: #6b7280;
}

/* Categories */
.categories-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.category-item {
  border-radius: 12px;
  background: #f9fafb;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.category-item:hover {
  background: #f0fdf4;
  border-color: #10b981;
  transform: translateX(5px);
}

.category-item.active {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.category-item a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  text-decoration: none;
  color: inherit;
  font-weight: 600;
}

.category-name {
  color: #1f2937;
  transition: color 0.3s ease;
}

.category-item.active .category-name {
  color: white;
}

.category-count {
  background: rgba(16, 185, 129, 0.15);
  color: #10b981;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 700;
  min-width: 30px;
  text-align: center;
}

.category-item.active .category-count {
  background: rgba(255, 255, 255, 0.2);
  color: white;
}

/* Newsletter Widget */
.newsletter-widget {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.newsletter-widget .widget-title::after {
  background: rgba(255, 255, 255, 0.3);
}

.newsletter-widget p {
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 1.5rem;
  font-size: 1rem;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.newsletter-form input {
  padding: 1rem;
  border: none;
  border-radius: 12px;
  font-size: 0.95rem;
  background: rgba(255, 255, 255, 0.15);
  color: white;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.newsletter-form input::placeholder {
  color: rgba(255, 255, 255, 0.8);
}

.newsletter-form input:focus {
  outline: none;
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
}

.newsletter-form button {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  min-height: 48px;
}

.newsletter-form button:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.newsletter-form button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* No Content Message */
.no-content-message {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
  font-style: italic;
  background: #f9fafb;
  border-radius: 12px;
  margin: 1rem 0;
}

/* Tags Cloud */
.tags-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.tag-cloud-item {
  background: #f0fdf4;
  color: #166534;
  padding: 0.6rem 1.2rem;
  border-radius: 25px;
  font-size: 0.85rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.tag-cloud-item:hover {
  background: #10b981;
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* Loading States */
.loading-state,
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 4rem 2rem;
  min-height: 60vh;
}

.loading-spinner {
  width: 60px;
  height: 60px;
  border: 6px solid #d1fae5;
  border-top: 6px solid #10b981;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 2rem;
}

.loading-spinner.small {
  width: 20px;
  height: 20px;
  border-width: 3px;
  margin: 0;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state {
  background: white;
  border-radius: 24px;
  margin: 2rem;
  box-shadow: 0 15px 40px rgba(220, 38, 38, 0.1);
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-state h2 {
  color: #dc2626;
  margin: 1rem 0 2rem;
  font-size: 1.5rem;
}

.retry-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  background: #b91c1c;
  transform: translateY(-2px);
}

.back-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border: 1px solid #10b981;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.back-link:hover {
  background: #10b981;
  color: white;
}

/* Responsive Design */
@media (max-width: 1400px) {
  .news-content-layout {
    grid-template-columns: 1fr 350px;
    gap: 2rem;
  }
}

@media (max-width: 1200px) {
  .news-article {
    padding: 2rem;
  }

  .news-title {
    font-size: 2.2rem;
  }
}

@media (max-width: 992px) {
  .news-content-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .news-detail-sidebar {
    order: 3;
  }

  .news-main-content {
    order: 1;
  }

  .related-news {
    order: 2;
  }

  .sidebar-widget {
    padding: 1.5rem;
  }

  .meta-group {
    flex-direction: column;
    gap: 1rem;
  }

  .news-actions {
    margin-top: 1rem;
  }
}

@media (max-width: 768px) {
  .news-detail-page {
    padding: 1rem 0;
  }

  .news-detail-container {
    padding: 0 1rem;
  }

  .breadcrumb-container {
    padding: 0.75rem 1rem;
    font-size: 0.8rem;
  }

  .breadcrumb-container span:last-child {
    max-width: 200px;
  }

  .news-article {
    padding: 1.5rem;
  }

  .news-title {
    font-size: 1.8rem;
  }

  .news-content {
    font-size: 1rem;
  }

  .reading-progress {
    margin: -1.5rem -1.5rem 2rem;
  }

  .related-news {
    padding: 1.5rem;
  }

  .related-news-grid {
    grid-template-columns: 1fr;
  }

  .share-buttons {
    flex-direction: column;
  }

  .share-btn {
    width: 100%;
  }

  .popular-news-item {
    flex-direction: column;
    gap: 1rem;
    align-items: center;
    text-align: center;
  }

  .sidebar-widget {
    padding: 1.25rem;
  }

  .widget-title {
    font-size: 1.2rem;
  }
}

@media (max-width: 480px) {
  .news-title {
    font-size: 1.6rem;
  }

  .section-header h2 {
    font-size: 1.5rem;
    flex-direction: column;
    gap: 0.5rem;
  }

  .gallery-grid {
    grid-template-columns: 1fr;
  }

  .tags-container {
    flex-direction: column;
    gap: 0.5rem;
  }

  .tag {
    text-align: center;
  }
}

/* Print Styles */
@media print {
  .news-detail-sidebar,
  .related-news,
  .news-share,
  .breadcrumb,
  .news-actions,
  .reading-progress {
    display: none !important;
  }

  .news-article {
    box-shadow: none;
    padding: 0;
  }

  .news-content-layout {
    grid-template-columns: 1fr;
  }
}
</style>

