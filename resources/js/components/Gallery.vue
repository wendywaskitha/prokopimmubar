<template>
  <div class="gallery-section">
    <div class="section-header">
      <h2>Galeri Foto</h2>
      <div class="section-divider"></div>
      <p class="section-description">Kumpulan foto terbaik dari berbagai kegiatan di Kabupaten Muna Barat</p>
    </div>
    
    <div class="loading" v-if="loading">
      <div class="loading-spinner"></div>
      <p>Memuat galeri foto...</p>
    </div>
    
    <div class="error" v-else-if="error">
      <i class="error-icon">⚠️</i>
      <p>{{ error }}</p>
    </div>
    
    <div class="gallery-content" v-else>
      <div class="gallery-grid">
        <div 
          class="gallery-item" 
          v-for="gallery in galleries" 
          :key="gallery.id"
          @click="openGallery(gallery.id)"
        >
          <div class="gallery-thumbnail">
            <img 
              :src="getFullImageUrl(gallery.images[0]?.image_path)" 
              :alt="gallery.title"
              class="thumbnail-image"
            >
            <div class="gallery-overlay">
              <div class="gallery-info">
                <h3 class="gallery-title">{{ gallery.title }}</h3>
                <p class="gallery-count">{{ gallery.images.length }} foto</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="view-all-container">
        <router-link to="/gallery" class="view-all-button">Lihat Semua Galeri</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';
import config from '../config';

export default {
  name: 'Gallery',
  data() {
    return {
      galleries: [],
      loading: false,
      error: null
    }
  },
  mounted() {
    this.fetchGalleries();
  },
  methods: {
    async fetchGalleries() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.getGalleries({ per_page: 6 });
        this.galleries = response.data.data;
      } catch (err) {
        this.error = 'Gagal memuat galeri foto. Silakan coba lagi.';
        console.error('Error fetching galleries:', err);
      } finally {
        this.loading = false;
      }
    },
    getFullImageUrl(imagePath) {
      return config.getStorageUrl(imagePath) || 'https://placehold.co/400x300?text=No+Image';
    },
    openGallery(id) {
      this.$router.push(`/gallery/${id}`);
    }
  }
}
</script>

<style scoped>
.gallery-section {
  max-width: 1200px;
  margin: 0 auto 4rem;
  padding: 0 2rem;
}

.section-header {
  text-align: center;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
}

.section-divider {
  width: 80px;
  height: 4px;
  background: linear-gradient(135deg, #10b981, #059669);
  margin: 1rem auto 2rem;
  border-radius: 2px;
}

.section-description {
  color: #6b7280;
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto;
}

.gallery-content {
  margin-top: 2rem;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.gallery-item {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.gallery-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.gallery-thumbnail {
  position: relative;
  width: 100%;
  padding-top: 75%; /* 4:3 Aspect Ratio */
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

.gallery-item:hover .thumbnail-image {
  transform: scale(1.05);
}

.gallery-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
  color: white;
  padding: 1.5rem;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
  opacity: 1;
}

.gallery-info {
  transform: translateY(10px);
  transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-info {
  transform: translateY(0);
}

.gallery-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 0.5rem;
}

.gallery-count {
  font-size: 0.9rem;
  opacity: 0.9;
  margin: 0;
}

.view-all-container {
  text-align: center;
  margin-top: 3rem;
}

.view-all-button {
  display: inline-block;
  background: #10b981;
  color: white;
  padding: 1rem 2rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.view-all-button:hover {
  background: #059669;
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
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
@media (max-width: 768px) {
  .gallery-section {
    padding: 0 1rem;
  }
  
  .gallery-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
  }
  
  .section-header h2 {
    font-size: 1.75rem;
  }
  
  .section-description {
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .gallery-grid {
    grid-template-columns: 1fr;
  }
  
  .gallery-overlay {
    opacity: 1;
  }
  
  .gallery-info {
    transform: translateY(0);
  }
}
</style>