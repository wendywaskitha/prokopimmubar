<template>
  <div class="hero-features">
    <div class="feature-item">
      <div class="feature-icon">📰</div>
      <div class="feature-text">
        <h4>Berita Terkini</h4>
        <p>Update informasi terbaru seputar Muna Barat</p>
      </div>
    </div>
    <div class="feature-item">
      <div class="feature-icon">📢</div>
      <div class="feature-text">
        <h4>Program Pemerintah</h4>
        <p>Kebijakan dan inisiatif pembangunan daerah</p>
      </div>
    </div>
    <div class="feature-item">
      <div class="feature-icon">👥</div>
      <div class="feature-text">
        <h4>Layanan Publik</h4>
        <p>Pelayanan terpadu untuk masyarakat Muna Barat</p>
      </div>
    </div>
    <div class="feature-item">
      <div class="feature-icon">🌳</div>
      <div class="feature-text">
        <h4>Potensi Daerah</h4>
        <p>Wisata, ekonomi kreatif, dan kekayaan alam</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HeroFeatures'
}
</script>

<style scoped>
.hero-features {
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  padding: 2rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  max-width: 1400px;
  width: 90%;
  margin: -60px auto 3rem;
  position: relative;
  z-index: 10;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.feature-item:hover {
  background: #f9fafb;
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}

.feature-icon {
  font-size: 1.8rem;
  width: 55px;
  height: 55px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.feature-text h4 {
  margin: 0 0 0.5rem;
  color: #1f2937;
  font-weight: 600;
}

.feature-text p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hero-features {
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 1.5rem;
  }

  .feature-item {
    flex-direction: column;
    text-align: center;
  }
  
  .feature-item:nth-child(1) {
    justify-self: end;
  }
  
  .feature-item:nth-child(2) {
    justify-self: start;
  }
  
  .feature-item:nth-child(3) {
    justify-self: end;
  }
  
  .feature-item:nth-child(4) {
    justify-self: start;
  }
}

@media (max-width: 768px) {
  .hero-features {
    width: 95%;
    padding: 1.5rem;
    margin: -30px auto 2rem;
    gap: 1.5rem;
  }
  
  .feature-icon {
    width: 50px;
    height: 50px;
    font-size: 1.5rem;
  }
  
  .feature-text h4 {
    font-size: 1rem;
  }
  
  .feature-text p {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .hero-features {
    padding: 1rem;
    gap: 1rem;
    margin-top: 1rem;
  }
  
  .feature-icon {
    width: 45px;
    height: 45px;
    font-size: 1.3rem;
  }
  
  .feature-text h4 {
    font-size: 0.9rem;
  }
  
  .feature-text p {
    font-size: 0.8rem;
  }
}

@media (max-width: 768px) {
  .hero-features {
    width: 95%;
    padding: 1.5rem;
    margin: -30px auto 2rem;
    gap: 1.5rem;
  }
  
  .feature-icon {
    width: 50px;
    height: 50px;
    font-size: 1.5rem;
  }
  
  .feature-text h4 {
    font-size: 1rem;
  }
  
  .feature-text p {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .hero-features {
    margin-top: 1rem;
  }
}
</style>