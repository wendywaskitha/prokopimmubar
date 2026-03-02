<template>
  <div class="categories-section">
    <div class="categories-grid">
      <div class="category-card education" @click="goToCategory('pendidikan')">
        <h3>Pendidikan</h3>
      </div>
      <div class="category-card environment" @click="goToCategory('lingkungan')">
        <h3>Lingkungan</h3>
      </div>
      <div class="category-card tourism" @click="goToCategory('pariwisata')">
        <h3>Pariwisata</h3>
      </div>
      <div class="category-card development" @click="goToCategory('pembangunan')">
        <h3>Pembangunan</h3>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CategoriesSection',
  methods: {
    goToCategory(category) {
      this.$router.push(`/news?category=${category}`);
    }
  }
}
</script>

<style scoped>
.categories-section {
  max-width: 1200px;
  margin: 0 auto 4rem;
  padding: 0 2rem;
}

.categories-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.category-card {
  height: 120px;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1.1rem;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.category-card:hover {
  transform: translateY(-5px);
}

.category-card.education {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}

.category-card.environment {
  background: linear-gradient(135deg, #10b981, #059669);
}

.category-card.tourism {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.category-card.development {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

/* Responsive Design */
@media (max-width: 768px) {
  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .categories-grid {
    grid-template-columns: 1fr;
  }
}
</style>