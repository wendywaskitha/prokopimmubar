<template>
  <div class="categories-page">
    <div class="categories-container">
      <h1>Kategori Berita</h1>
      <div class="categories-grid">
        <div 
          v-for="category in categories" 
          :key="category.id"
          class="category-card"
        >
          <router-link :to="`/news?category=${category.slug}`" class="category-link">
            <div class="category-icon">
              <span v-html="category.icon"></span>
            </div>
            <h3>{{ category.name }}</h3>
            <p>{{ category.description }}</p>
            <div class="category-stats">
              <span>{{ category.news_count }} berita</span>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NewsCategories',
  data() {
    return {
      categories: [
        {
          id: 1,
          name: 'Pemerintahan',
          slug: 'pemerintahan',
          description: 'Berita seputar kegiatan pemerintahan daerah',
          news_count: 24,
          icon: '🏛️'
        },
        {
          id: 2,
          name: 'Pembangunan',
          slug: 'pembangunan',
          description: 'Progres pembangunan infrastruktur daerah',
          news_count: 18,
          icon: '🏗️'
        },
        {
          id: 3,
          name: 'Kesehatan',
          slug: 'kesehatan',
          description: 'Program kesehatan masyarakat',
          news_count: 15,
          icon: '🏥'
        },
        {
          id: 4,
          name: 'Pendidikan',
          slug: 'pendidikan',
          description: 'Kegiatan pendidikan di daerah',
          news_count: 12,
          icon: '📚'
        },
        {
          id: 5,
          name: 'Wisata',
          slug: 'wisata',
          description: 'Potensi pariwisata daerah',
          news_count: 8,
          icon: '🌴'
        },
        {
          id: 6,
          name: 'Olahraga',
          slug: 'olahraga',
          description: 'Prestasi dan kegiatan olahraga',
          news_count: 6,
          icon: '⚽'
        }
      ]
    }
  }
}
</script>

<style scoped>
.categories-page {
  min-height: 100vh;
  background: #f8fffe;
  padding: 2rem 0;
}

.categories-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.categories-container h1 {
  text-align: center;
  font-size: 2.5rem;
  font-weight: 800;
  color: #1f2937;
  margin-bottom: 3rem;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.category-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.category-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.category-link {
  text-decoration: none;
  color: inherit;
  display: block;
  padding: 2rem;
  text-align: center;
}

.category-icon {
  font-size: 3rem;
  margin-bottom: 1.5rem;
}

.category-card h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
}

.category-card p {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.category-stats {
  display: flex;
  justify-content: center;
}

.category-stats span {
  background: #f0fdf4;
  color: #166534;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

@media (max-width: 768px) {
  .categories-container {
    padding: 0 1rem;
  }
  
  .categories-container h1 {
    font-size: 2rem;
  }
  
  .categories-grid {
    grid-template-columns: 1fr;
  }
}
</style>