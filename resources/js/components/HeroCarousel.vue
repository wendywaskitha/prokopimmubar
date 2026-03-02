<template>
  <div class="hero-carousel-wrapper">
    <div class="hero-carousel" v-if="heroes.length > 0">
      <div class="carousel-container">
        <div
          class="carousel-slide"
          v-for="(hero, index) in heroes"
          :key="hero.id"
          :class="{ 'active': index === currentSlide }"
        >
          <img :src="getFullImageUrl(hero.image)" :alt="hero.title" />
          <div class="carousel-content">
            <h2>{{ hero.title }}</h2>
            <p>{{ hero.description }}</p>
            <router-link v-if="hero.link" :to="hero.link" class="btn">
              Selengkapnya
            </router-link>
          </div>
        </div>
      </div>

      <div class="carousel-controls" v-if="heroes.length > 1">
        <button @click="prevSlide" class="carousel-btn prev-btn">&lt;</button>
        <button @click="nextSlide" class="carousel-btn next-btn">&gt;</button>

        <div class="carousel-indicators">
          <span
            v-for="(hero, index) in heroes"
            :key="hero.id"
            :class="{ 'active': index === currentSlide }"
            @click="goToSlide(index)"
          ></span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';
import config from '../config';

export default {
  name: 'HeroCarousel',
  data() {
    return {
      heroes: [],
      currentSlide: 0,
      interval: null
    }
  },
  async mounted() {
    await this.fetchHeroes();
    this.startAutoplay();
  },
  beforeUnmount() {
    this.stopAutoplay();
  },
  methods: {
    async fetchHeroes() {
      try {
        const response = await api.getHeroes();
        if (response.data.success) {
          this.heroes = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching heroes:', error);
      }
    },
    getFullImageUrl(imagePath) {
      return config.getStorageUrl(imagePath);
    },
    nextSlide() {
      this.currentSlide = (this.currentSlide + 1) % this.heroes.length;
    },
    prevSlide() {
      this.currentSlide = (this.currentSlide - 1 + this.heroes.length) % this.heroes.length;
    },
    goToSlide(index) {
      this.currentSlide = index;
    },
    startAutoplay() {
      this.interval = setInterval(() => {
        this.nextSlide();
      }, 5000); // Change slide every 5 seconds
    },
    stopAutoplay() {
      if (this.interval) {
        clearInterval(this.interval);
      }
    }
  }
}
</script>

<style scoped>
.hero-carousel-wrapper {
  margin-top: 0;
  margin-bottom: 2rem;
}

.hero-carousel {
  position: relative;
  width: 100%;
  height: 750px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  border-radius: 0;
}

.carousel-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.carousel-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 0.5s ease-in-out;
}

.carousel-slide.active {
  opacity: 1;
}

.carousel-slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1);
  transition: transform 5s ease-in-out;
}

.carousel-slide.active img {
  transform: scale(1.05);
}

.carousel-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.85));
  color: white;
  padding: 2rem 1.25rem;
  text-align: center;
  z-index: 5;
}

.carousel-content h2 {
  margin: 0 0 0.75rem 0;
  font-size: 1.6rem;
  font-weight: 800;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  line-height: 1.2;
  animation: fadeInUp 0.8s ease forwards;
  opacity: 0;
  transform: translateY(20px);
}

.carousel-slide.active .carousel-content h2 {
  animation-delay: 0.3s;
}

.carousel-content p {
  margin: 0 0 1.25rem 0;
  font-size: 0.95rem;
  max-width: 100%;
  margin-left: auto;
  margin-right: auto;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
  line-height: 1.4;
  animation: fadeInUp 0.8s ease forwards;
  opacity: 0;
  transform: translateY(20px);
}

.carousel-slide.active .carousel-content p {
  animation-delay: 0.5s;
}

.carousel-controls {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  transform: translateY(-50%);
  display: flex;
  justify-content: space-between;
  padding: 0 1rem;
  z-index: 10;
}

.carousel-btn {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 10;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
}

.carousel-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.carousel-indicators {
  position: absolute;
  bottom: 1.25rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 0.5rem;
  z-index: 10;
}

.carousel-indicators span {
  display: block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.carousel-indicators span.active {
  background: white;
  border: 2px solid #007bff;
  transform: scale(1.2);
}

.btn {
  display: inline-block;
  padding: 0.7rem 1.75rem;
  background-color: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 30px;
  transition: all 0.3s ease;
  font-weight: 600;
  font-size: 0.95rem;
  border: 2px solid #007bff;
  box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
  animation: fadeInUp 0.8s ease forwards;
  opacity: 0;
  transform: translateY(20px);
}

.carousel-slide.active .btn {
  animation-delay: 0.7s;
}

.btn:hover {
  background-color: #0056b3;
  border-color: #0056b3;
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
}

/* Animations */
@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive styles */
@media (max-width: 1200px) {
  .hero-carousel {
    height: 650px;
  }

  .carousel-content {
    padding: 2.5rem 2rem;
  }

  .carousel-content h2 {
    font-size: 2.2rem;
  }

  .carousel-content p {
    font-size: 1.1rem;
  }
}

@media (max-width: 1024px) {
  .hero-carousel {
    height: 550px;
  }

  .carousel-content {
    padding: 2.25rem 1.75rem;
  }

  .carousel-content h2 {
    font-size: 2rem;
  }

  .carousel-content p {
    font-size: 1.05rem;
  }

  .carousel-btn {
    width: 45px;
    height: 45px;
    font-size: 1.3rem;
  }
}

@media (max-width: 768px) {
  .hero-carousel {
    height: 450px;
    border-radius: 0;
  }

  .carousel-content {
    padding: 2rem 1.5rem;
  }

  .carousel-content h2 {
    font-size: 1.7rem;
  }

  .carousel-content p {
    font-size: 1rem;
  }

  .carousel-btn {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
  }

  .carousel-indicators {
    bottom: 1.5rem;
  }

  .carousel-indicators span {
    width: 10px;
    height: 10px;
  }

  .btn {
    padding: 0.8rem 2rem;
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .hero-carousel {
    height: 400px;
  }

  .carousel-content {
    padding: 1.75rem 1.25rem;
  }

  .carousel-content h2 {
    font-size: 1.5rem;
  }

  .carousel-content p {
    font-size: 0.95rem;
  }

  .carousel-btn {
    width: 35px;
    height: 35px;
    font-size: 1.1rem;
  }

  .carousel-indicators {
    bottom: 1.25rem;
  }

  .carousel-indicators span {
    width: 9px;
    height: 9px;
  }

  .btn {
    padding: 0.7rem 1.75rem;
    font-size: 0.95rem;
  }
}

@media (max-width: 480px) {
  .hero-carousel {
    height: 350px;
  }

  .carousel-content {
    padding: 1.5rem 1rem;
  }

  .carousel-content h2 {
    font-size: 1.4rem;
  }

  .carousel-content p {
    font-size: 0.9rem;
  }

  .carousel-btn {
    width: 32px;
    height: 32px;
    font-size: 1rem;
  }

  .carousel-indicators {
    bottom: 1rem;
  }

  .carousel-indicators span {
    width: 8px;
    height: 8px;
  }

  .btn {
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
  }
}

/* Untuk layar sangat kecil */
@media (max-width: 360px) {
  .hero-carousel {
    height: 300px;
  }

  .carousel-content {
    padding: 1.25rem 0.75rem;
  }

  .carousel-content h2 {
    font-size: 1.25rem;
  }

  .carousel-content p {
    font-size: 0.85rem;
  }

  .carousel-btn {
    width: 30px;
    height: 30px;
    font-size: 0.9rem;
  }

  .carousel-indicators {
    bottom: 0.9rem;
  }

  .carousel-indicators span {
    width: 7px;
    height: 7px;
  }

  .btn {
    padding: 0.5rem 1.25rem;
    font-size: 0.85rem;
  }
}

</style>
