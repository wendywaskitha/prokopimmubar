<template>
  <div class="social-media-page">
    <!-- Hero Header -->
    <div class="social-hero">
      <div class="social-hero-content">
        <h1>Media Sosial</h1>
        <p>Ikuti aktivitas dan informasi terkini melalui platform media sosial resmi kami</p>
      </div>
    </div>

    <div class="social-container">
      <!-- Platform Filters -->
      <div class="social-filters">
        <div class="filter-header">
          <h2>Filter Platform</h2>
          <p>Pilih platform media sosial untuk melihat konten spesifik</p>
        </div>

        <div class="platform-filters">
          <button
            v-for="platform in platforms"
            :key="platform.value"
            :class="{ active: selectedPlatform === platform.value }"
            @click="selectPlatform(platform.value)"
            class="platform-btn"
          >
            <span class="platform-icon" v-html="platform.icon"></span>
            <span class="platform-name">{{ platform.name }}</span>
            <span class="platform-count">{{ platform.count }}</span>
          </button>
        </div>

        <div class="filter-actions">
          <div class="search-section">
            <div class="search-input-group">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Cari konten media sosial..."
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

          <div class="sort-section">
            <select class="sort-select">
              <option value="latest">Terbaru</option>
              <option value="popular">Terpopuler</option>
              <option value="most-viewed">Paling Dilihat</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="social-content">
        <!-- Main Content -->
        <div class="social-main">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Memuat konten media sosial...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <p>{{ error }}</p>
            <button @click="loadContent" class="retry-btn">Coba Lagi</button>
          </div>

          <!-- Social Media Grid -->
          <div v-else>
            <!-- Results Info -->
            <div class="results-info">
              <h3>{{ getFilterTitle() }}</h3>
              <p>Menampilkan {{ socialItems.length }} konten</p>
            </div>

            <!-- Featured Content -->
            <div class="featured-content" v-if="featuredItem">
              <div class="featured-item">
                <div class="featured-video">
                  <div class="video-container large">
                    <iframe
                      width="100%"
                      height="400"
                      :src="featuredItem.embedUrl"
                      frameborder="0"
                      allowfullscreen
                    ></iframe>
                  </div>
                  <div class="featured-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polygon points="12,2 15.09,8.26 22,9 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9 8.91,8.26"/>
                    </svg>
                    Konten Unggulan
                  </div>
                </div>
                <div class="featured-details">
                  <div class="platform-tag featured">
                    <span class="platform-icon" v-html="featuredItem.platformIcon"></span>
                    {{ featuredItem.platform }}
                  </div>
                  <h2>{{ featuredItem.title }}</h2>
                  <p>{{ featuredItem.description }}</p>
                  <div class="featured-stats">
                    <div class="stat-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                      {{ featuredItem.views }}
                    </div>
                    <div class="stat-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                      </svg>
                      {{ featuredItem.likes }}
                    </div>
                    <div class="stat-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                      </svg>
                      {{ featuredItem.comments }}
                    </div>
                    <div class="stat-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12,6 12,12 16,14"/>
                      </svg>
                      {{ featuredItem.date }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Social Media Grid -->
            <div class="social-media-grid">
              <div class="social-media-item" v-for="(item, index) in socialItems" :key="index">
                <div class="item-header">
                  <div class="platform-info">
                    <div class="platform-tag" :class="item.platform.toLowerCase()">
                      <span class="platform-icon" v-html="item.platformIcon"></span>
                      {{ item.platform }}
                    </div>
                    <div class="item-date">{{ item.date }}</div>
                  </div>
                  <button class="item-menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="1"/>
                      <circle cx="12" cy="5" r="1"/>
                      <circle cx="12" cy="19" r="1"/>
                    </svg>
                  </button>
                </div>

                <div class="video-container">
                  <div v-if="item.platform === 'tiktok'" class="tiktok-embed-container" :id="'tiktok-embed-' + item.id" v-html="generateEmbedCode(item)"></div>
                  <div v-else v-html="generateEmbedCode(item)"></div>
                  <div class="video-overlay">
                    <button class="play-btn">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="5,3 19,12 5,21"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <div class="item-content">
                  <h3 class="item-title">{{ item.title || `Judul Video ${index + 1}` }}</h3>
                  <p class="item-description">{{ item.description || `Deskripsi singkat video ${index + 1} dari channel resmi Pemerintah Kabupaten Muna Barat.` }}</p>

                  <div class="item-engagement">
                    <div class="engagement-stats">
                      <button class="engagement-btn like">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        {{ item.likes || Math.floor(Math.random() * 500) + 50 }}
                      </button>
                      <button class="engagement-btn comment">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        {{ item.comments || Math.floor(Math.random() * 100) + 10 }}
                      </button>
                      <button class="engagement-btn share">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8M16 6l-4-4-4 4M12 2v13"/>
                        </svg>
                        {{ item.shares || Math.floor(Math.random() * 50) + 5 }}
                      </button>
                    </div>

                    <div class="view-count">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                      {{ item.views || `${Math.floor(Math.random() * 50)}k penayangan` }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="socialItems.length === 0" class="empty-state">
              <div class="empty-icon">📱</div>
              <h3>Tidak Ada Konten Ditemukan</h3>
              <p>Belum ada konten untuk platform yang dipilih</p>
              <button @click="selectedPlatform = 'all'" class="reset-filter-btn">Lihat Semua Platform</button>
            </div>

            <!-- Load More Button -->
            <div class="load-more-section" v-if="hasMore">
              <button class="load-more-btn" @click="loadMore" :disabled="loadingMore">
                <span v-if="loadingMore">
                  <div class="loading-spinner small"></div>
                  Memuat...
                </span>
                <span v-else>
                  Muat Lebih Banyak
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  </svg>
                </span>
              </button>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper" v-if="totalPages > 1">
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

                <button class="page-btn next" @click="nextPage" :disabled="currentPage === totalPages">
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
        <aside class="social-sidebar">
          <!-- Social Stats -->
          <div class="sidebar-widget stats-widget">
            <h3 class="widget-title">Statistik Media Sosial</h3>
            <div class="social-stats">
              <div class="stat-card youtube">
                <div class="stat-icon">📺</div>
                <div class="stat-info">
                  <div class="stat-number">{{ getPlatformCount('youtube') }}+</div>
                  <div class="stat-label">Konten</div>
                </div>
              </div>
              <div class="stat-card tiktok">
                <div class="stat-icon">🎵</div>
                <div class="stat-info">
                  <div class="stat-number">{{ getPlatformCount('tiktok') }}+</div>
                  <div class="stat-label">Konten</div>
                </div>
              </div>
              <div class="stat-card instagram">
                <div class="stat-icon">📸</div>
                <div class="stat-info">
                  <div class="stat-number">{{ getPlatformCount('instagram') }}+</div>
                  <div class="stat-label">Konten</div>
                </div>
              </div>
              <div class="stat-card facebook">
                <div class="stat-icon">👍</div>
                <div class="stat-info">
                  <div class="stat-number">{{ getPlatformCount('facebook') }}+</div>
                  <div class="stat-label">Konten</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Popular Content -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Konten Terbaru</h3>
            <div class="popular-list">
              <div class="popular-item" v-for="(item, index) in socialItems.slice(0, 3)" :key="item.id">
                <div class="popular-thumbnail">
                  <div class="popular-platform" :class="item.platform.toLowerCase()">
                    <span v-html="item.platformIcon"></span>
                  </div>
                </div>
                <div class="popular-content">
                  <h4>{{ item.title }}</h4>
                  <div class="popular-stats">
                    <span class="views">{{ item.views }}</span>
                    <span class="date">{{ item.date }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Follow Us -->
          <div class="sidebar-widget follow-widget">
            <h3 class="widget-title">Ikuti Kami</h3>
            <p>Dapatkan update terbaru melalui media sosial resmi kami</p>
            <div class="social-links">
              <a href="#" class="social-link youtube">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
                YouTube
              </a>
              <a href="#" class="social-link instagram">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
                Instagram
              </a>
              <a href="#" class="social-link facebook">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
              </a>
              <a href="#" class="social-link tiktok">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                </svg>
                TikTok
              </a>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'SocialMedia',
  data() {
    return {
      selectedPlatform: 'all',
      searchQuery: '',
      currentPage: 1,
      totalPages: 1,
      loading: false,
      loadingMore: false,
      error: null,
      hasMore: true,
      platforms: [
        {
          name: 'Semua',
          value: 'all',
          count: 0,
          icon: '🌐'
        },
        {
          name: 'YouTube',
          value: 'youtube',
          count: 0,
          icon: this.getYoutubeIcon()
        },
        {
          name: 'TikTok',
          value: 'tiktok',
          count: 0,
          icon: this.getTiktokIcon()
        },
        {
          name: 'Instagram',
          value: 'instagram',
          count: 0,
          icon: this.getInstagramIcon()
        },
        {
          name: 'Facebook',
          value: 'facebook',
          count: 0,
          icon: this.getFacebookIcon()
        }
      ],
      socialItems: [],
      featuredItem: null,
      popularContent: [],
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0
      }
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
  mounted() {
    this.loadSocialMedia();
    // Tambahkan event listener untuk menangani error embed
    this.addEmbedErrorHandling();
  },
  methods: {
    async loadTikTokEmbedScript() {
      // Periksa apakah script sudah dimuat
      if (document.getElementById('tiktok-embed-script')) {
        return Promise.resolve();
      }
      
      return new Promise((resolve, reject) => {
        // Buat script element
        const script = document.createElement('script');
        script.id = 'tiktok-embed-script';
        script.src = 'https://www.tiktok.com/embed.js';
        script.async = true;
        script.onload = resolve;
        script.onerror = reject;
        
        // Tambahkan ke head
        document.head.appendChild(script);
      });
    },
    async loadSocialMedia() {
      this.loading = true;
      this.error = null;
      
      try {
        const params = {
          page: this.currentPage,
          per_page: 15
        };
        
        // Add search query if present
        if (this.searchQuery) {
          // Note: The API doesn't currently support search, but we can filter client-side
          // In a real implementation, you would add search to the API
        }
        
        let response;
        // Use different endpoints based on platform selection
        if (this.selectedPlatform !== 'all') {
          response = await axios.get(`/api/v1/social-media/platform/${this.selectedPlatform}`, { params });
        } else {
          response = await axios.get('/api/v1/social-media', { params });
        }
        
        if (response.data.success) {
          let items = response.data.data;
          
          // Apply client-side search filtering if needed
          if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            items = items.filter(item => 
              (item.title && item.title.toLowerCase().includes(query)) ||
              (item.description && item.description.toLowerCase().includes(query)) ||
              (item.platform && item.platform.toLowerCase().includes(query))
            );
          }
          
          // Map API data to component data structure
          this.socialItems = items.map(item => ({
            id: item.id,
            title: item.title || 'Untitled',
            description: item.description || 'No description available',
            platform: item.platform ? this.capitalizeFirstLetter(item.platform) : 'Unknown',
            platformIcon: this.getPlatformIcon(item.platform),
            embedUrl: item.url ? this.getEmbedUrl(item) : 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            url: item.url || '',
            embed_code: item.embed_code || '',
            date: item.created_at ? this.formatDate(item.created_at) : 'Unknown date',
            // Note: views, likes, comments, shares are not in the model
            // Using random values as placeholders
            views: `${Math.floor(Math.random() * 100)}k`,
            likes: Math.floor(Math.random() * 500) + 50,
            comments: Math.floor(Math.random() * 100) + 10,
            shares: Math.floor(Math.random() * 50) + 5
          }));
          
          // Render TikTok embeds
          this.$nextTick(() => {
            // Muat script TikTok jika ada konten TikTok
            if (this.socialItems.some(item => item.platform.toLowerCase() === 'tiktok')) {
              this.loadTikTokEmbedScript().catch(() => {
                console.warn('Failed to load TikTok embed script');
              });
            }
          });
          
          // Update pagination (client-side search affects this)
          if (this.searchQuery) {
            // For client-side search, we need to handle pagination differently
            this.pagination = {
              current_page: 1,
              last_page: 1,
              per_page: 15,
              total: this.socialItems.length
            };
            this.totalPages = 1;
            this.currentPage = 1;
          } else {
            // Use server pagination
            this.pagination = response.data.pagination;
            this.totalPages = response.data.pagination.last_page;
            this.currentPage = response.data.pagination.current_page;
          }
          
          // Set featured item (first item as featured)
          if (this.socialItems.length > 0) {
            this.featuredItem = { ...this.socialItems[0] };
          }
          
          // Update platform counts
          this.updatePlatformCounts();
        } else {
          this.error = 'Gagal memuat data media sosial';
        }
      } catch (error) {
        console.error('Error loading social media:', error);
        if (error.response) {
          // Server responded with error status
          this.error = `Error ${error.response.status}: ${error.response.statusText}`;
        } else if (error.request) {
          // Request was made but no response received
          this.error = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        } else {
          // Something else happened
          this.error = 'Terjadi kesalahan saat memuat media sosial';
        }
      } finally {
        this.loading = false;
      }
    },
    getPlatformIcon(platform) {
      switch (platform.toLowerCase()) {
        case 'youtube':
          return this.getYoutubeIcon();
        case 'tiktok':
          return this.getTiktokIcon();
        case 'instagram':
          return this.getInstagramIcon();
        case 'facebook':
          return this.getFacebookIcon();
        default:
          return '🌐';
      }
    },
    getYoutubeIcon() {
      return '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
    },
    getTiktokIcon() {
      return '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>';
    },
    getInstagramIcon() {
      return '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>';
    },
    getFacebookIcon() {
      return '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
    },
    getEmbedUrl(item) {
      // Extract video ID and create embed URL based on platform
      if (item.platform === 'youtube' && item.url) {
        const videoId = this.extractYouTubeId(item.url);
        return videoId ? `https://www.youtube.com/embed/${videoId}` : 'https://www.youtube.com/embed/dQw4w9WgXcQ';
      }
      
      // Untuk platform lain, kembalikan URL langsung
      return item.url || 'https://www.youtube.com/embed/dQw4w9WgXcQ';
    },
    
    // Fungsi untuk menghasilkan embed code berdasarkan platform dengan penanganan error
    // Platform yang didukung: youtube, tiktok, instagram, facebook, twitter
    generateEmbedCode(item) {
      try {
        switch (item.platform.toLowerCase()) {
          case 'youtube':
            const youtubeId = this.extractYouTubeId(item.url);
            if (youtubeId) {
              return `<iframe width="100%" height="250" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allowfullscreen></iframe>`;
            }
            break;
            
          case 'tiktok':
            // Untuk TikTok, kita akan menggunakan blockquote yang akan diubah oleh script TikTok
            const tiktokId = this.extractTikTokId(item.url);
            if (tiktokId) {
              return `<blockquote class="tiktok-embed" cite="${item.url}" data-video-id="${tiktokId}"><section><a href="${item.url}" target="_blank" rel="noopener">Lihat video TikTok</a></section></blockquote>`;
            }
            break;
            
          case 'instagram':
            // Untuk Instagram, kita bisa menggunakan URL langsung atau embed code jika tersedia
            const instagramId = this.extractInstagramId(item.url);
            if (instagramId) {
              return `<iframe width="100%" height="250" src="${item.url}/embed" frameborder="0" allowfullscreen></iframe>`;
            }
            break;
            
          case 'facebook':
            // Untuk Facebook, kita bisa menggunakan embed code jika tersedia
            if (item.embed_code) {
              return item.embed_code;
            }
            // Fallback ke URL langsung dalam iframe
            return `<iframe width="100%" height="250" src="${item.url}" frameborder="0" allowfullscreen></iframe>`;
            
          case 'twitter':
            // Untuk Twitter, kita bisa menggunakan URL langsung atau embed code jika tersedia
            if (item.embed_code) {
              return item.embed_code;
            }
            // Fallback ke URL langsung dalam iframe
            return `<iframe width="100%" height="250" src="${item.url}" frameborder="0" allowfullscreen></iframe>`;
            
          default:
            // Untuk platform lain, gunakan URL langsung dalam iframe
            return `<iframe width="100%" height="250" src="${item.url}" frameborder="0" allowfullscreen></iframe>`;
        }
      } catch (error) {
        console.error(`Error generating embed code for ${item.platform}:`, error);
      }
      
      // Fallback default
      return `<iframe width="100%" height="250" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>`;
    },

    // Ekstrak ID video dari URL berdasarkan platform
    extractVideoId(url, platform) {
      switch (platform.toLowerCase()) {
        case 'youtube':
          return this.extractYouTubeId(url);
        case 'tiktok':
          return this.extractTikTokId(url);
        case 'instagram':
          return this.extractInstagramId(url);
        case 'facebook':
          return this.extractFacebookId(url);
        case 'twitter':
          return this.extractTwitterId(url);
        default:
          return null;
      }
    },

    // Ekstrak ID video YouTube dari URL
    extractYouTubeId(url) {
      const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
      const match = url.match(regExp);
      return (match && match[2].length === 11) ? match[2] : null;
    },

    // Ekstrak ID video TikTok dari URL
    extractTikTokId(url) {
      // Contoh URL: https://www.tiktok.com/@username/video/1234567891234567891
      const match = url.match(/\/video\/(\d+)/);
      return match ? match[1] : null;
    },

    // Ekstrak ID video Instagram dari URL
    extractInstagramId(url) {
      // Contoh URL: https://www.instagram.com/p/ID_VIDEO/
      const match = url.match(/\/p\/([^\/]+)/);
      return match ? match[1] : null;
    },

    // Ekstrak ID video Facebook dari URL
    extractFacebookId(url) {
      // Untuk Facebook, kita akan menggunakan URL langsung
      return url;
    },

    // Ekstrak ID tweet Twitter dari URL
    extractTwitterId(url) {
      // Untuk Twitter, kita akan menggunakan URL langsung
      return url;
    },

    getFilterTitle() {
      if (this.selectedPlatform === 'all') return 'Semua Konten Media Sosial';
      const platform = this.platforms.find(p => p.value === this.selectedPlatform);
      return platform ? `Konten ${platform.name}` : 'Konten Media Sosial';
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.currentPage = page;
        this.loadSocialMedia();
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
    handleSearch() {
      this.currentPage = 1;
      this.loadSocialMedia();
    },
    selectPlatform(platform) {
      this.selectedPlatform = platform;
      this.currentPage = 1;
      this.loadSocialMedia();
    },
    updatePlatformCounts() {
      // In a real implementation, you would get these counts from the API
      // For now, we'll just set some sample values based on the current data
      this.platforms.forEach(platform => {
        if (platform.value === 'all') {
          platform.count = this.pagination.total;
        } else {
          // Count items for this platform
          const count = this.socialItems.filter(item => 
            item.platform.toLowerCase() === platform.value
          ).length;
          platform.count = count;
        }
      });
    },
    getPlatformCount(platform) {
      const plat = this.platforms.find(p => p.value === platform);
      return plat ? plat.count : 0;
    },
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    formatDate(dateString) {
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    
    // Tambahkan event listener untuk menangani error embed
    addEmbedErrorHandling() {
      // Tangani error untuk iframe
      window.addEventListener('error', (e) => {
        if (e.target.tagName === 'IFRAME') {
          console.warn('Embed iframe failed to load:', e.target.src);
          // Di sini Anda bisa menambahkan logika untuk menampilkan pesan error kepada pengguna
        }
      }, true);

      // Tangani error untuk script
      window.addEventListener('error', (e) => {
        if (e.target.tagName === 'SCRIPT') {
          console.warn('Script failed to load:', e.target.src);
        }
      }, true);
    },

    // Fungsi untuk menampilkan pesan error yang ramah pengguna
    showEmbedError(platform) {
      // Di sini Anda bisa menambahkan logika untuk menampilkan pesan error yang ramah pengguna
      console.warn(`Failed to load embed for ${platform}`);
    }
  }
}
</script>

<style scoped>
.social-media-page {
  min-height: 100vh;
  background: #f8fffe;
}

/* Hero Section */
.social-hero {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 4rem 0 3rem;
  text-align: center;
}

.social-hero-content h1 {
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.social-hero-content p {
  font-size: 1.2rem;
  opacity: 0.95;
  margin: 0;
}

/* Container */
.social-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Filters */
.social-filters {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  padding: 2rem;
  margin: -2rem 0 3rem;
  position: relative;
  z-index: 10;
}

.filter-header {
  text-align: center;
  margin-bottom: 2rem;
}

.filter-header h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem;
}

.filter-header p {
  color: #6b7280;
  margin: 0;
}

.platform-filters {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.platform-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
}

.platform-btn:hover {
  border-color: #10b981;
  background: #f0fdf4;
}

.platform-btn.active {
  border-color: #10b981;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.platform-icon {
  display: flex;
  align-items: center;
  margin-right: 0.75rem;
}

.platform-name {
  flex: 1;
  font-weight: 600;
}

.platform-count {
  background: rgba(255,255,255,0.2);
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.platform-btn.active .platform-count {
  background: rgba(255,255,255,0.3);
}

.platform-btn:not(.active) .platform-count {
  background: #10b981;
  color: white;
}

.filter-actions {
  display: flex;
  gap: 2rem;
  align-items: end;
}

.search-section {
  flex: 1;
}

.search-section label {
  display: block;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
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

.sort-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  background: white;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  min-width: 150px;
}

.sort-select:focus {
  outline: none;
  border-color: #10b981;
}

/* Content Layout */
.social-content {
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

.results-info h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.results-info p {
  margin: 0;
  color: #6b7280;
  font-weight: 500;
}

/* Featured Content */
.featured-content {
  margin-bottom: 3rem;
}

.featured-item {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.featured-video {
  position: relative;
}

.video-container.large {
  position: relative;
  overflow: hidden;
}

.featured-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  backdrop-filter: blur(10px);
  z-index: 2;
}

.featured-details {
  padding: 2rem;
}

.platform-tag {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 15px;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.platform-tag.featured {
  background: #f0fdf4;
  color: #166534;
}

.platform-tag.youtube {
  background: #fef2f2;
  color: #dc2626;
}

.platform-tag.tiktok {
  background: #f3f4f6;
  color: #1f2937;
}

.platform-tag.instagram {
  background: #fdf2f8;
  color: #be185d;
}

.platform-tag.facebook {
  background: #eff6ff;
  color: #1d4ed8;
}

.featured-details h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
  line-height: 1.3;
}

.featured-details p {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.featured-stats {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.featured-stats .stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #9ca3af;
  font-size: 0.9rem;
}

/* Social Media Grid */
.social-media-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 2rem;
}

.social-media-item {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.social-media-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f3f4f6;
}

.platform-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.item-date {
  color: #9ca3af;
  font-size: 0.85rem;
}

.item-menu {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.item-menu:hover {
  background: #f3f4f6;
  color: #6b7280;
}

.video-container {
  position: relative;
  overflow: hidden;
}

.video-container iframe {
  width: 100%;
  height: 250px;
  border: none;
}

.tiktok-embed-container {
  width: 100%;
  height: 250px;
  overflow: hidden;
}

.tiktok-embed-container blockquote {
  width: 100%;
  height: 100%;
}

.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.video-container:hover .video-overlay {
  opacity: 1;
}

.play-btn {
  background: rgba(255,255,255,0.9);
  color: #10b981;
  border: none;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.play-btn:hover {
  background: white;
  transform: scale(1.1);
}

.item-content {
  padding: 1.5rem;
}

.item-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 1rem;
  line-height: 1.4;
}

.item-description {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.item-engagement {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.engagement-stats {
  display: flex;
  gap: 1rem;
}

.engagement-btn {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.engagement-btn:hover {
  background: #f3f4f6;
  color: #6b7280;
}

.engagement-btn.like:hover {
  color: #dc2626;
}

.engagement-btn.comment:hover {
  color: #2563eb;
}

.engagement-btn.share:hover {
  color: #10b981;
}

.view-count {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #9ca3af;
  font-size: 0.85rem;
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

.reset-filter-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 1rem;
}

/* Load More */
.load-more-section {
  text-align: center;
  margin: 3rem 0;
}

.load-more-btn {
  background: white;
  color: #10b981;
  border: 2px solid #10b981;
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.load-more-btn:hover:not(:disabled) {
  background: #10b981;
  color: white;
}

.load-more-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
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
.social-sidebar {
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

.social-stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.stat-card {
  background: rgba(255,255,255,0.1);
  border-radius: 15px;
  padding: 1.5rem;
  text-align: center;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2);
}

.stat-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.stat-number {
  font-size: 1.5rem;
  font-weight: 800;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.85rem;
  opacity: 0.9;
}

/* Popular Content */
.popular-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.popular-item {
  display: flex;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.popular-item:last-child {
  border-bottom: none;
}

.popular-thumbnail {
  position: relative;
  flex-shrink: 0;
}

.popular-thumbnail img {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
}

.popular-platform {
  position: absolute;
  bottom: -5px;
  right: -5px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid white;
  font-size: 0.7rem;
}

.popular-platform.youtube {
  background: #dc2626;
  color: white;
}

.popular-platform.tiktok {
  background: #1f2937;
  color: white;
}

.popular-platform.instagram {
  background: #be185d;
  color: white;
}

.popular-platform.facebook {
  background: #1d4ed8;
  color: white;
}

.popular-content h4 {
  margin: 0 0 0.5rem;
  font-size: 0.95rem;
  color: #1f2937;
  line-height: 1.3;
}

.popular-stats {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #9ca3af;
}

/* Follow Widget */
.follow-widget {
  background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
  color: white;
}

.follow-widget .widget-title::after {
  background: rgba(255,255,255,0.3);
}

.follow-widget p {
  color: rgba(255,255,255,0.9);
  margin-bottom: 1.5rem;
}

.social-links {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.social-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  color: white;
  text-decoration: none;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  transition: all 0.3s ease;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
}

.social-link:hover {
  background: rgba(255,255,255,0.2);
  transform: translateX(3px);
}

.social-link.youtube:hover {
  background: #dc2626;
}

.social-link.instagram:hover {
  background: #be185d;
}

.social-link.facebook:hover {
  background: #1d4ed8;
}

.social-link.tiktok:hover {
  background: #1f2937;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .social-content {
    grid-template-columns: 1fr 300px;
    gap: 2rem;
  }

  .social-media-grid {
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  }
}

@media (max-width: 992px) {
  .social-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .social-sidebar {
    order: -1;
  }

  .social-stats {
    grid-template-columns: repeat(4, 1fr);
  }

  .platform-filters {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .social-hero-content h1 {
    font-size: 2.5rem;
  }

  .social-container {
    padding: 0 1rem;
  }

  .social-filters {
    padding: 1.5rem;
    margin: -1.5rem 0 2rem;
  }

  .platform-filters {
    grid-template-columns: 1fr;
  }

  .filter-actions {
    flex-direction: column;
    gap: 1rem;
  }

  .social-media-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .featured-stats {
    flex-direction: column;
    gap: 1rem;
  }

  .pagination {
    flex-wrap: wrap;
    gap: 0.25rem;
  }

  .social-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .social-hero-content h1 {
    font-size: 2rem;
  }

  .item-content {
    padding: 1rem;
  }

  .featured-details {
    padding: 1.5rem;
  }

  .engagement-stats {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
}
</style>