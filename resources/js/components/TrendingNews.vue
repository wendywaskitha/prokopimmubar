<template>
  <div class="trending-sidebar">
    <h3>Sedang Trending 🔥</h3>
    <div class="trending-list">
      <div class="trending-item" v-for="(news, index) in newsItems.slice(0, 5)" :key="'trend-' + news.id" @click="goToNews(news.slug)">
        <span class="trending-number">{{ index + 1 }}</span>
        <div class="trending-content">
          <div class="trending-image">
            <img :src="getFullImageUrl(news.featured_image) || 'https://placehold.co/100x60'" :alt="news.title" @error="handleImageError">
          </div>
          <div class="trending-text">
            <h4>{{ news.title }}</h4>
            <span class="trending-views">{{ formatViews(news.views || 0) }} views</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import config from '../config';

export default {
  name: 'TrendingNews',
  props: {
    newsItems: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    goToNews(slug) {
      this.$router.push(`/news/${slug}`);
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
      return config.getStorageUrl(imagePath);
    },
    handleImageError(event) {
      // Set placeholder image on error
      event.target.src = 'https://placehold.co/100x60';
    }
  }
}
</script>

<style scoped>
.trending-sidebar {
  background: #f9fafb;
  padding: 2rem;
  border-radius: 15px;
  height: fit-content;
}

.trending-sidebar h3 {
  margin: 0 0 1.5rem;
  color: #1f2937;
  font-size: 1.2rem;
}

.trending-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.trending-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: background-color 0.3s ease;
}

.trending-item:hover {
  background-color: #f3f4f6;
}

.trending-number {
  background: #10b981;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 600;
  flex-shrink: 0;
}

.trending-content {
  display: flex;
  gap: 1rem;
}

.trending-image img {
  width: 100px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  flex-shrink: 0;
}

.trending-text h4 {
  margin: 0 0 0.25rem;
  font-size: 0.9rem;
  line-height: 1.3;
  color: #1f2937;
}

.trending-views {
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .trending-sidebar {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .trending-sidebar {
    padding: 1rem;
  }
  
  .trending-content {
    flex-direction: column;
  }
  
  .trending-image {
    display: none;
  }
}
</style>