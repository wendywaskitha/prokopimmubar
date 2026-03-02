<template>
  <div class="featured-section">
    <div class="section-header">
      <h2>Galeri Unggulan</h2>
      <router-link to="/gallery" class="view-all-link">Lihat Semua</router-link>
    </div>

    <div class="loading" v-if="loading">
      <div class="loading-spinner"></div>
      <p>Memuat galeri...</p>
    </div>
    <div class="error" v-else-if="error">
      <i class="error-icon">⚠️</i>
      <p>{{ error }}</p>
    </div>
    <div class="featured-grid" v-else>
      <div class="featured-card large" v-if="galleries.length > 0" @click="goToGallery(galleries[0].id)">
        <img :src="getFullImageUrl(galleries[0].images[0].image_path)" :alt="galleries[0].title">
        <div class="card-overlay">
          <div class="card-content">
            <h3>{{ galleries[0].title }}</h3>
            <p>Koleksi foto terbaru dari aktivitas daerah</p>
          </div>
        </div>
      </div>

      <div class="featured-cards-small">
        <div class="featured-card small" v-for="(gallery, index) in galleries.slice(1, 5)" :key="index" @click="goToGallery(gallery.id)">
          <img :src="getFullImageUrl(gallery.images[0].image_path)" :alt="gallery.title">
          <div class="card-overlay">
            <div class="card-content">
              <h4>{{ gallery.title }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'FeaturedGalleries',
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
        const response = await api.getGalleries({ per_page: 5 });
        this.galleries = response.data.data;
      } catch (err) {
        this.error = 'Gagal memuat galeri. Silakan coba lagi.';
        console.error('Error fetching galleries:', err);
      } finally {
        this.loading = false;
      }
    },
    getFullImageUrl(imagePath) {
      if (imagePath.startsWith('http')) {
        return imagePath;
      }
      const cleanPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath;
      return `http://localhost:8000/storage/${cleanPath}`;
    },
    goToGallery(id) {
      this.$router.push(`/gallery/${id}`);
    }
  }
}
</script>

<style scoped>
.featured-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  margin-bottom: 4rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.view-all-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.view-all-link:hover {
  color: #059669;
}

.featured-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
  height: 400px;
}

.featured-card {
  position: relative;
  border-radius: 15px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.featured-card:hover {
  transform: translateY(-5px);
}

.featured-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  color: white;
  padding: 2rem;
}

.featured-card.large .card-content h3 {
  font-size: 1.5rem;
  margin: 0 0 0.5rem;
  font-weight: 700;
}

.featured-cards-small {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.featured-card.small .card-content h4 {
  font-size: 1rem;
  margin: 0;
  font-weight: 600;
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
  .featured-section {
    padding: 0 1.5rem;
  }
  
  .section-header h2 {
    font-size: 1.75rem;
  }
  
  .featured-grid {
    height: 350px;
  }
  
  .card-overlay {
    padding: 1.5rem;
  }
  
  .featured-card.large .card-content h3 {
    font-size: 1.3rem;
  }
}

@media (max-width: 768px) {
  .featured-section {
    padding: 0 1rem;
    margin-bottom: 3rem;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  
  .section-header h2 {
    font-size: 1.5rem;
  }
  
  .featured-grid {
    grid-template-columns: 1fr;
    grid-template-rows: auto auto;
    height: auto;
    gap: 1rem;
  }
  
  .featured-card.large {
    order: 1;
  }
  
  .featured-cards-small {
    order: 2;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  
  .card-overlay {
    padding: 1.25rem;
  }
  
  .featured-card.large .card-content h3 {
    font-size: 1.25rem;
  }
  
  .featured-card.small .card-content h4 {
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  .featured-section {
    padding: 0 0.75rem;
    margin-bottom: 2.5rem;
  }
  
  .section-header h2 {
    font-size: 1.35rem;
  }
  
  .view-all-link {
    font-size: 0.9rem;
  }
  
  .featured-grid {
    gap: 0.75rem;
  }
  
  .featured-cards-small {
    gap: 0.75rem;
  }
  
  .card-overlay {
    padding: 1rem;
  }
  
  .featured-card.large .card-content h3 {
    font-size: 1.1rem;
  }
  
  .featured-card.large .card-content p {
    font-size: 0.9rem;
  }
  
  .featured-card.small .card-content h4 {
    font-size: 0.85rem;
  }
}
</style>