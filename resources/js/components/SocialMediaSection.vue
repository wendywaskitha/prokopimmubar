<template>
  <div class="social-section">
    <div class="section-header">
      <h2>Video Galeri</h2>
      <div class="section-divider"></div>
    </div>
    <div class="loading" v-if="loading">
      <div class="loading-spinner"></div>
      <p>Memuat video galeri...</p>
    </div>
    <div class="error" v-else-if="error">
      <i class="error-icon">⚠️</i>
      <p>{{ error }}</p>
    </div>
    <div class="social-media-content" v-else>
      <div class="video-gallery" v-if="socialMediaItems.length > 0">
        <div class="video-grid">
          <div
            class="video-card"
            v-for="item in socialMediaItems"
            :key="item.id"
            @click="openVideoModal(item)"
          >
            <div class="video-thumbnail">
              <img
                :src="getThumbnailUrl(item)"
                :alt="item.title"
                class="thumbnail-image"
              >
              <div class="play-overlay">
                <div class="play-icon">▶</div>
              </div>
              <div class="platform-badge" :class="item.platform">
                {{ getPlatformName(item.platform) }}
              </div>
            </div>
            <div class="video-info">
              <h3 class="video-title">{{ item.title }}</h3>
              <p class="video-description">{{ item.description }}</p>
              <div class="video-meta">
                <span class="video-date">{{ formatDate(item.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="no-content">
        <i class="social-icon">🎥</i>
        <p>Belum ada video dalam galeri.</p>
      </div>
    </div>
  </div>

  <!-- Video Modal -->
  <div class="video-modal" v-if="selectedVideo" @click="closeVideoModal">
    <div class="modal-content" @click.stop>
      <span class="close-button" @click="closeVideoModal">&times;</span>
      <div class="video-wrapper">
        <div v-if="selectedVideo.embed_code" v-html="selectedVideo.embed_code"></div>
        <div v-else class="no-embed">
          <p>Video tidak dapat dimuat.</p>
        </div>
      </div>
      <div class="modal-video-info">
        <h3>{{ selectedVideo.title }}</h3>
        <p>{{ selectedVideo.description }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'SocialMediaSection',
  data() {
    return {
      socialMediaItems: [],
      loading: false,
      error: null,
      selectedVideo: null
    }
  },
  mounted() {
    this.fetchSocialMedia();
  },
  methods: {
    async fetchSocialMedia() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.getSocialMedia({ per_page: 6 }); // Mengambil lebih banyak video
        this.socialMediaItems = response.data.data;
      } catch (err) {
        this.error = 'Gagal memuat video galeri. Silakan coba lagi.';
        console.error('Error fetching social media:', err);
      } finally {
        this.loading = false;
      }
    },
    getThumbnailUrl(item) {
      // Jika ada thumbnail khusus, gunakan itu
      if (item.thumbnail) {
        return this.getFullImageUrl(item.thumbnail);
      }

      // Jika tidak, coba ekstrak dari embed code atau gunakan placeholder
      try {
        if (item.embed_code) {
          // Untuk YouTube
          if (item.platform === 'youtube') {
            const videoId = this.extractYouTubeId(item.embed_code);
            if (videoId) {
              return `https://img.youtube.com/vi/${videoId}/mqdefault.jpg`;
            }
          }
          // Untuk TikTok dan platform lain bisa ditambahkan di sini
        }
      } catch (e) {
        console.error('Error extracting thumbnail:', e);
      }

      // Placeholder jika tidak ada thumbnail
      return 'https://placehold.co/400x225?text=Video+Thumbnail';
    },
    extractYouTubeId(embedCode) {
      const regex = /youtube\.com\/embed\/([a-zA-Z0-9_-]+)/;
      const match = embedCode.match(regex);
      return match ? match[1] : null;
    },
    getPlatformName(platform) {
      const platformNames = {
        youtube: 'YouTube',
        tiktok: 'TikTok',
        instagram: 'Instagram',
        facebook: 'Facebook',
        twitter: 'Twitter'
      };
      return platformNames[platform] || platform;
    },
    getFullImageUrl(imagePath) {
      if (imagePath.startsWith('http')) {
        return imagePath;
      }
      const cleanPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath;
      return `http://localhost:8000/storage/${cleanPath}`;
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    openVideoModal(video) {
      this.selectedVideo = video;
      document.body.style.overflow = 'hidden'; // Mencegah scrolling saat modal terbuka
    },
    closeVideoModal() {
      this.selectedVideo = null;
      document.body.style.overflow = ''; // Mengembalikan scrolling
    }
  }
}
</script>

<style scoped>
.social-section {
  max-width: 1200px;
  margin: 0 auto 4rem;
  padding: 3rem 1rem; /* Menambahkan padding horizontal */
}

.section-header {
  text-align: center;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.section-divider {
  width: 80px;
  height: 4px;
  background: linear-gradient(135deg, #10b981, #059669);
  margin: 1rem auto 2rem;
  border-radius: 2px;
}

.social-media-content {
  text-align: center;
}

/* Video Gallery Styles */
.video-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.video-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.video-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.video-thumbnail {
  position: relative;
  width: 100%;
  padding-top: 56.25%; /* 16:9 Aspect Ratio */
  overflow: hidden;
}

.thumbnail-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.video-card:hover .thumbnail-image {
  transform: scale(1.05);
}

.play-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.video-card:hover .play-overlay {
  opacity: 1;
}

.play-icon {
  width: 50px; /* Mengurangi ukuran ikon play */
  height: 50px; /* Mengurangi ukuran ikon play */
  background: rgba(255, 255, 255, 0.9);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem; /* Mengurangi ukuran font */
  color: #10b981;
}

.platform-badge {
  position: absolute;
  top: 0.5rem; /* Mengurangi jarak dari atas */
  right: 0.5rem; /* Mengurangi jarak dari kanan */
  padding: 0.2rem 0.6rem; /* Mengurangi padding */
  border-radius: 20px;
  font-size: 0.7rem; /* Mengurangi ukuran font */
  font-weight: 600;
  color: white;
  text-transform: uppercase;
}

.video-info {
  padding: 1rem; /* Mengurangi padding */
}

.video-title {
  font-size: 1rem; /* Mengurangi ukuran font */
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.video-description {
  font-size: 0.85rem; /* Mengurangi ukuran font */
  color: #6b7280;
  margin: 0 0 0.75rem; /* Mengurangi margin bawah */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.video-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.75rem; /* Mengurangi ukuran font */
  color: #9ca3af;
}

.video-date {
  font-weight: 500;
}

/* Video Modal Styles */
.video-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.95); /* Meningkatkan opacity untuk fokus yang lebih baik */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: #fff;
  border-radius: 12px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
}

.close-button {
  position: absolute;
  top: 0.75rem; /* Mengurangi jarak dari atas */
  right: 0.75rem; /* Mengurangi jarak dari kanan */
  font-size: 1.8rem; /* Mengurangi ukuran font */
  color: white;
  cursor: pointer;
  z-index: 1001;
  background: rgba(0, 0, 0, 0.6); /* Mengurangi opacity background */
  width: 36px; /* Mengurangi ukuran tombol */
  height: 36px; /* Mengurangi ukuran tombol */
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s ease;
}

.close-button:hover {
  background: rgba(0, 0, 0, 0.8);
}

.video-wrapper {
  position: relative;
  width: 100%;
  padding-top: 56.25%; /* 16:9 Aspect Ratio */
}

.video-wrapper div {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.video-wrapper iframe {
  width: 100%;
  height: 100%;
  border: none;
}

.no-embed {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  background: #f3f4f6;
  color: #6b7280;
}

.modal-video-info {
  padding: 1.25rem; /* Mengurangi padding */
}

.modal-video-info h3 {
  font-size: 1.3rem; /* Mengurangi ukuran font */
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.75rem; /* Mengurangi margin bawah */
}

.modal-video-info p {
  color: #6b7280;
  line-height: 1.5; /* Mengurangi line height */
  margin: 0;
  font-size: 0.95rem; /* Mengurangi ukuran font */
}

/* No Content Styles */
.no-content {
  padding: 3rem;
  color: #6b7280;
}

.social-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 1rem;
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
  .social-section {
    padding: 2rem 1rem; /* Menambahkan padding horizontal */
    margin-bottom: 3rem;
  }
  
  .section-header h2 {
    font-size: 1.75rem;
  }
  
  .video-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
  }
  
  .video-title {
    font-size: 1rem;
  }
  
  .video-description {
    font-size: 0.85rem;
  }
}

@media (max-width: 768px) {
  .social-section {
    padding: 1.5rem 1rem; /* Menambahkan padding horizontal */
    margin-bottom: 2.5rem;
  }
  
  .section-header {
    margin-bottom: 1.5rem;
  }
  
  .section-header h2 {
    font-size: 1.5rem;
  }
  
  .section-divider {
    width: 60px;
    height: 3px;
    margin: 0.75rem auto 1.5rem;
  }
  
  .video-grid {
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); /* Mengurangi min-width */
    gap: 1.25rem;
    margin-top: 1.5rem;
  }
  
  .modal-content {
    max-width: 95%;
    max-height: 85vh;
  }
  
  .modal-video-info {
    padding: 1.25rem;
  }
  
  .modal-video-info h3 {
    font-size: 1.25rem;
  }
  
  .close-button {
    top: 0.75rem;
    right: 0.75rem;
    width: 35px;
    height: 35px;
    font-size: 1.5rem;
  }
  
  .video-wrapper {
    padding-top: 56.25%;
  }
}

@media (max-width: 576px) {
  .social-section {
    padding: 1rem 0.75rem; /* Mengurangi padding horizontal */
    margin-bottom: 2rem;
  }
  
  .section-header h2 {
    font-size: 1.4rem;
  }
  
  .video-grid {
    grid-template-columns: 1fr; /* Satu kolom penuh */
    gap: 1rem;
    margin-top: 1rem;
  }
  
  .video-card {
    border-radius: 10px;
  }
  
  .video-info {
    padding: 0.875rem; /* Mengurangi padding */
  }
  
  .video-title {
    font-size: 0.95rem;
  }
  
  .video-description {
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
  }
  
  .video-meta {
    font-size: 0.75rem;
  }
  
  .platform-badge {
    padding: 0.2rem 0.5rem; /* Mengurangi padding */
    font-size: 0.65rem; /* Mengurangi ukuran font */
  }
  
  .play-icon {
    width: 45px; /* Mengurangi ukuran */
    height: 45px; /* Mengurangi ukuran */
    font-size: 1.1rem; /* Mengurangi ukuran font */
  }
  
  .modal-content {
    max-width: 98%;
    max-height: 80vh;
  }
  
  .modal-video-info {
    padding: 0.875rem; /* Mengurangi padding */
  }
  
  .modal-video-info h3 {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
  }
  
  .modal-video-info p {
    font-size: 0.9rem;
  }
  
  .close-button {
    top: 0.5rem;
    right: 0.5rem;
    width: 30px;
    height: 30px;
    font-size: 1.25rem;
  }
  
  .loading,
  .error,
  .no-content {
    padding: 1.5rem 0.75rem; /* Mengurangi padding */
  }
  
  .loading-spinner {
    width: 25px; /* Mengurangi ukuran */
    height: 25px; /* Mengurangi ukuran */
    border-width: 2px; /* Mengurangi lebar border */
  }
  
  .error-icon,
  .social-icon {
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
  }
}

@media (max-width: 400px) {
  .social-section {
    padding: 0.75rem 0.5rem; /* Mengurangi padding horizontal */
    margin-bottom: 1.5rem;
  }
  
  .section-header h2 {
    font-size: 1.25rem; /* Mengurangi ukuran font */
  }
  
  .section-divider {
    width: 50px; /* Mengurangi lebar */
    margin: 0.5rem auto 1rem; /* Mengurangi margin */
  }
  
  .video-info {
    padding: 0.75rem; /* Mengurangi padding */
  }
  
  .video-title {
    font-size: 0.9rem; /* Mengurangi ukuran font */
  }
  
  .video-description {
    font-size: 0.75rem; /* Mengurangi ukuran font */
  }
  
  .platform-badge {
    padding: 0.15rem 0.4rem; /* Mengurangi padding */
    font-size: 0.6rem; /* Mengurangi ukuran font */
  }
  
  .play-icon {
    width: 40px; /* Mengurangi ukuran */
    height: 40px; /* Mengurangi ukuran */
    font-size: 1rem; /* Mengurangi ukuran font */
  }
  
  .modal-video-info {
    padding: 0.75rem; /* Mengurangi padding */
  }
  
  .modal-video-info h3 {
    font-size: 1rem; /* Mengurangi ukuran font */
  }
  
  .modal-video-info p {
    font-size: 0.85rem; /* Mengurangi ukuran font */
  }
  
  .close-button {
    width: 25px; /* Mengurangi ukuran */
    height: 25px; /* Mengurangi ukuran */
    font-size: 1.1rem; /* Mengurangi ukuran font */
  }
  
  .loading,
  .error,
  .no-content {
    padding: 1.25rem 0.5rem; /* Mengurangi padding */
  }
  
  .loading-spinner {
    width: 20px; /* Mengurangi ukuran */
    height: 20px; /* Mengurangi ukuran */
    border-width: 2px; /* Mengurangi lebar border */
  }
  
  .error-icon,
  .social-icon {
    font-size: 1.25rem; /* Mengurangi ukuran */
    margin-bottom: 0.5rem; /* Mengurangi margin */
  }
}
</style>
