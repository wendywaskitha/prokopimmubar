<template>
  <div class="gallery-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Memuat galeri...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h2>{{ error }}</h2>
      <button @click="loadGallery" class="retry-btn">Coba Lagi</button>
      <router-link to="/gallery" class="back-link">Kembali ke Galeri</router-link>
    </div>

    <!-- Gallery Content -->
    <div v-else-if="gallery" class="gallery-detail-container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <router-link to="/">Beranda</router-link>
        <span>/</span>
        <router-link to="/gallery">Galeri</router-link>
        <span>/</span>
        <span>{{ gallery.title }}</span>
      </nav>

      <div class="gallery-content-layout">
        <!-- Main Content -->
        <main class="gallery-main-content">
          <article class="gallery-article">
            <!-- Gallery Header -->
            <header class="gallery-header">
              <h1 class="gallery-title">{{ gallery.title }}</h1>

              <div class="gallery-meta">
                <div class="meta-item" v-if="gallery.news">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <span>Terhubung dengan berita: 
                    <router-link :to="`/news/${gallery.news.slug}`" class="news-link">
                      {{ gallery.news.title }}
                    </router-link>
                  </span>
                </div>

                <div class="meta-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                  <span>{{ formatDate(gallery.created_at) }}</span>
                </div>

                <div class="meta-item">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <span>{{ gallery.images.length }} foto</span>
                </div>
              </div>
            </header>

            <!-- Gallery Description -->
            <div class="gallery-description" v-if="gallery.description">
              <p>{{ gallery.description }}</p>
            </div>

            <!-- Gallery Images -->
            <div class="gallery-images">
              <div class="image-grid">
                <div 
                  v-for="(image, index) in gallery.images" 
                  :key="image.id"
                  class="image-item"
                  @click="openLightbox(index)"
                >
                  <img :src="getFullImageUrl(image.image_path)" :alt="image.caption || `Gallery image ${index + 1}`">
                  <div class="image-overlay">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <path d="M12 8v8M8 12h8"></path>
                    </svg>
                  </div>
                  <div class="image-caption" v-if="image.caption">
                    {{ image.caption }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Share Section -->
            <div class="gallery-share">
              <h3>Bagikan Galeri:</h3>
              <div class="share-buttons">
                <button class="share-btn facebook" @click="shareToFacebook">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                  Facebook
                </button>
                <button class="share-btn twitter" @click="shareToTwitter">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                  Twitter
                </button>
                <button class="share-btn whatsapp" @click="shareToWhatsApp">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                  </svg>
                  WhatsApp
                </button>
              </div>
            </div>
          </article>

          <!-- Related Galleries -->
          <section class="related-galleries" v-if="relatedGalleries.length > 0">
            <h2>Galeri Terkait</h2>
            <div class="related-galleries-grid">
              <div 
                v-for="related in relatedGalleries" 
                :key="related.id"
                class="related-gallery-item"
              >
                <router-link :to="`/gallery/${related.id}`" class="related-gallery-link">
                  <div class="related-image" v-if="related.images && related.images.length > 0">
                    <img :src="getFullImageUrl(related.images[0].image_path)" :alt="related.title">
                  </div>
                  <div class="related-content">
                    <h3>{{ related.title }}</h3>
                    <div class="related-meta">
                      <span>{{ formatDate(related.created_at) }}</span>
                      <span>{{ related.images.length }} foto</span>
                    </div>
                  </div>
                </router-link>
              </div>
            </div>
          </section>
        </main>

        <!-- Sidebar -->
        <aside class="gallery-detail-sidebar">
          <!-- Gallery Info Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Informasi Galeri</h3>
            <div class="gallery-info">
              <div class="info-item">
                <strong>Jumlah Foto:</strong>
                <span>{{ gallery.images.length }}</span>
              </div>
              <div class="info-item">
                <strong>Tanggal Dibuat:</strong>
                <span>{{ formatDate(gallery.created_at) }}</span>
              </div>
              <div class="info-item" v-if="gallery.news">
                <strong>Berita Terkait:</strong>
                <router-link :to="`/news/${gallery.news.slug}`" class="related-news-link">
                  {{ gallery.news.title }}
                </router-link>
              </div>
            </div>
          </div>

          <!-- Popular Galleries Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Galeri Populer</h3>
            <div class="popular-galleries-list">
              <div class="popular-gallery-item" v-for="(item, index) in popularGalleries" :key="item.id">
                <div class="popular-gallery-rank">{{ index + 1 }}</div>
                <div class="popular-gallery-content">
                  <h4>
                    <router-link :to="`/gallery/${item.id}`">{{ item.title }}</router-link>
                  </h4>
                  <div class="popular-gallery-meta">
                    <span>{{ item.images.length }} foto</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <!-- Lightbox -->
    <div v-if="lightboxOpen" class="lightbox" @click="closeLightbox">
      <div class="lightbox-content" @click.stop>
        <button class="lightbox-close" @click="closeLightbox">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        
        <button class="lightbox-nav prev" @click.stop="prevImage" :disabled="currentImageIndex === 0">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>
        
        <div class="lightbox-image-container">
          <img 
            :src="getFullImageUrl(gallery.images[currentImageIndex].image_path)" 
            :alt="gallery.images[currentImageIndex].caption || 'Gallery image'"
            class="lightbox-image"
          >
          <div class="lightbox-caption" v-if="gallery.images[currentImageIndex].caption">
            {{ gallery.images[currentImageIndex].caption }}
          </div>
        </div>
        
        <button class="lightbox-nav next" @click.stop="nextImage" :disabled="currentImageIndex === gallery.images.length - 1">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>
        
        <div class="lightbox-indicators">
          <span 
            v-for="(image, index) in gallery.images" 
            :key="image.id"
            :class="{ active: index === currentImageIndex }"
            @click.stop="goToImage(index)"
          ></span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'GalleryDetail',
  data() {
    return {
      gallery: null,
      relatedGalleries: [],
      popularGalleries: [],
      loading: true,
      error: null,
      lightboxOpen: false,
      currentImageIndex: 0
    }
  },
  mounted() {
    this.loadGallery();
    this.loadPopularGalleries();
  },
  methods: {
    getFullImageUrl(imagePath) {
      // If the image path is already a full URL, return it as is
      if (imagePath.startsWith('http')) {
        return imagePath;
      }
      
      // For Laravel storage paths, we need to prefix with /storage
      // Remove leading slash if present to avoid double slashes
      const cleanPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath;
      return `/storage/${cleanPath}`;
    },
    
    async loadGallery() {
      this.loading = true;
      this.error = null;
      
      try {
        const id = this.$route.params.id;
        const response = await api.getGalleryById(id);
        
        if (response.data.success) {
          this.gallery = response.data.data;
          await this.loadRelatedGalleries();
        } else {
          this.error = 'Galeri tidak ditemukan';
        }
      } catch (error) {
        console.error('Error loading gallery:', error);
        if (error.response && error.response.status === 404) {
          this.error = 'Galeri tidak ditemukan';
        } else {
          this.error = 'Terjadi kesalahan saat memuat galeri';
        }
      } finally {
        this.loading = false;
      }
    },
    
    async loadRelatedGalleries() {
      try {
        // For now, we'll just get some random galleries as related
        // In a real implementation, you might want to filter by news or tags
        const response = await api.getGalleries({ per_page: 4 });
        
        if (response.data.success) {
          // Filter out the current gallery
          this.relatedGalleries = response.data.data
            .filter(gallery => gallery.id !== this.gallery.id)
            .slice(0, 3);
        }
      } catch (error) {
        console.error('Error loading related galleries:', error);
      }
    },
    
    async loadPopularGalleries() {
      try {
        const response = await api.getGalleries({ per_page: 5 });
        
        if (response.data.success) {
          this.popularGalleries = response.data.data;
        }
      } catch (error) {
        console.error('Error loading popular galleries:', error);
      }
    },
    
    formatDate(dateString) {
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    
    openLightbox(index) {
      this.currentImageIndex = index;
      this.lightboxOpen = true;
      document.body.style.overflow = 'hidden';
    },
    
    closeLightbox() {
      this.lightboxOpen = false;
      document.body.style.overflow = '';
    },
    
    prevImage() {
      if (this.currentImageIndex > 0) {
        this.currentImageIndex--;
      }
    },
    
    nextImage() {
      if (this.currentImageIndex < this.gallery.images.length - 1) {
        this.currentImageIndex++;
      }
    },
    
    goToImage(index) {
      this.currentImageIndex = index;
    },
    
    shareToFacebook() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.gallery.title);
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&t=${title}`, '_blank');
    },
    
    shareToTwitter() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.gallery.title);
      window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank');
    },
    
    shareToWhatsApp() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.gallery.title);
      window.open(`https://wa.me/?text=${title}%20${url}`, '_blank');
    }
  },
  watch: {
    '$route'(to, from) {
      if (to.params.id !== from.params.id) {
        this.loadGallery();
      }
    }
  }
}
</script>

<style scoped>
.gallery-detail-page {
  min-height: 100vh;
  background: #f8fffe;
  padding: 2rem 0;
}

.gallery-detail-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 2rem;
  font-size: 0.9rem;
}

.breadcrumb a {
  color: #10b981;
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.breadcrumb span:last-child {
  color: #6b7280;
}

/* Content Layout */
.gallery-content-layout {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
}

/* Main Content */
.gallery-main-content {
  order: 1;
}

/* Gallery Article */
.gallery-article {
  background: white;
  border-radius: 20px;
  padding: 3rem;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  margin-bottom: 3rem;
}

/* Gallery Header */
.gallery-header {
  margin-bottom: 2rem;
}

.gallery-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1f2937;
  margin: 0 0 1.5rem;
  line-height: 1.2;
}

.gallery-meta {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.95rem;
}

.news-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 500;
}

.news-link:hover {
  text-decoration: underline;
}

/* Gallery Description */
.gallery-description {
  margin: 2rem 0;
  color: #374151;
  line-height: 1.8;
  font-size: 1.1rem;
}

.gallery-description p {
  margin: 0;
}

/* Gallery Images */
.gallery-images {
  margin: 2rem 0;
}

.image-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.image-item {
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  position: relative;
  aspect-ratio: 4/3;
}

.image-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.3s ease;
}

.image-item:hover img {
  transform: scale(1.05);
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-item:hover .image-overlay {
  opacity: 1;
}

.image-overlay svg {
  color: white;
}

.image-caption {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 0.5rem;
  font-size: 0.8rem;
  text-align: center;
  transform: translateY(100%);
  transition: transform 0.3s ease;
}

.image-item:hover .image-caption {
  transform: translateY(0);
}

/* Share Section */
.gallery-share {
  margin: 3rem 0 1rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.gallery-share h3 {
  margin: 0 0 1rem;
  color: #1f2937;
  font-size: 1.2rem;
}

.share-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.share-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
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

.share-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Related Galleries */
.related-galleries {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  margin-bottom: 3rem;
}

.related-galleries h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 2rem;
}

.related-galleries-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.related-gallery-item {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 20px rgba(16, 185, 129, 0.1);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.related-gallery-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
}

.related-gallery-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.related-image {
  height: 150px;
  overflow: hidden;
}

.related-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.related-gallery-item:hover .related-image img {
  transform: scale(1.05);
}

.related-content {
  padding: 1.5rem;
}

.related-content h3 {
  margin: 0 0 1rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #1f2937;
  line-height: 1.4;
}

.related-meta {
  display: flex;
  justify-content: space-between;
  color: #6b7280;
  font-size: 0.85rem;
}

/* Sidebar */
.gallery-detail-sidebar {
  order: 2;
}

.sidebar-widget {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  margin-bottom: 2rem;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.widget-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1.5rem;
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

/* Gallery Info */
.gallery-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.info-item:last-child {
  border-bottom: none;
}

.info-item strong {
  color: #1f2937;
}

.info-item span,
.info-item a {
  color: #6b7280;
  text-decoration: none;
}

.info-item a:hover {
  color: #10b981;
  text-decoration: underline;
}

/* Popular Galleries */
.popular-galleries-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.popular-gallery-item {
  display: flex;
  gap: 1rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #f3f4f6;
}

.popular-gallery-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.popular-gallery-rank {
  font-size: 1.5rem;
  font-weight: 700;
  color: #10b981;
  min-width: 30px;
}

.popular-gallery-content h4 {
  margin: 0 0 0.5rem;
  font-size: 1rem;
  line-height: 1.4;
}

.popular-gallery-content h4 a {
  color: #1f2937;
  text-decoration: none;
  transition: color 0.3s ease;
}

.popular-gallery-content h4 a:hover {
  color: #10b981;
}

.popular-gallery-meta {
  font-size: 0.8rem;
  color: #9ca3af;
}

/* Lightbox */
.lightbox {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.lightbox-content {
  position: relative;
  max-width: 90%;
  max-height: 90%;
  display: flex;
  align-items: center;
}

.lightbox-close {
  position: absolute;
  top: -40px;
  right: 0;
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0.5rem;
  z-index: 1001;
}

.lightbox-nav {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  cursor: pointer;
  padding: 1rem;
  margin: 0 1rem;
  border-radius: 50%;
  transition: background 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.lightbox-nav:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.3);
}

.lightbox-nav:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.lightbox-image-container {
  max-width: 80vw;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.lightbox-image {
  max-width: 100%;
  max-height: 70vh;
  object-fit: contain;
  border-radius: 4px;
}

.lightbox-caption {
  color: white;
  margin-top: 1rem;
  text-align: center;
  max-width: 80vw;
}

.lightbox-indicators {
  position: absolute;
  bottom: -40px;
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.lightbox-indicators span {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: background 0.3s ease;
}

.lightbox-indicators span.active {
  background: white;
}

/* States */
.loading-state,
.error-state {
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

.error-state h2 {
  color: #dc2626;
  margin: 1rem 0;
}

.retry-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin: 1rem 0;
}

.back-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 500;
}

.back-link:hover {
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .gallery-content-layout {
    grid-template-columns: 1fr 300px;
    gap: 1.5rem;
  }
  
  .gallery-article {
    padding: 2rem;
  }
  
  .gallery-title {
    font-size: 2rem;
  }
}

@media (max-width: 992px) {
  .gallery-content-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .gallery-detail-sidebar {
    order: 1;
  }
  
  .gallery-main-content {
    order: 2;
  }
  
  .sidebar-widget {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .gallery-detail-page {
    padding: 1rem 0;
  }
  
  .gallery-detail-container {
    padding: 0 1rem;
  }
  
  .gallery-article {
    padding: 1.5rem;
  }
  
  .gallery-title {
    font-size: 1.8rem;
  }
  
  .gallery-meta {
    flex-direction: column;
    gap: 1rem;
  }
  
  .related-galleries {
    padding: 1.5rem;
  }
  
  .related-galleries-grid {
    grid-template-columns: 1fr;
  }
  
  .share-buttons {
    flex-direction: column;
  }
  
  .share-btn {
    width: 100%;
    justify-content: center;
  }
  
  .image-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
  }
  
  .lightbox-content {
    max-width: 95%;
    max-height: 95%;
  }
  
  .lightbox-image-container {
    max-width: 95vw;
    max-height: 70vh;
  }
  
  .lightbox-image {
    max-height: 60vh;
  }
  
  .lightbox-caption {
    max-width: 95vw;
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  .gallery-description {
    font-size: 1rem;
  }
  
  .image-grid {
    grid-template-columns: 1fr;
  }
  
  .widget-title {
    font-size: 1.2rem;
  }
  
  .lightbox-content {
    flex-direction: column;
    padding: 2rem 0;
  }
  
  .lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    margin: 0;
  }
  
  .lightbox-nav.prev {
    left: 10px;
  }
  
  .lightbox-nav.next {
    right: 10px;
  }
  
  .lightbox-image-container {
    margin: 0 40px;
  }
}
</style>