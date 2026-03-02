<template>
  <div class="testimonials-section">
    <div class="testimonials-header">
      <h2>Testimoni Warga</h2>
      <p>Pembaca dan Kontributor Berbicara Tentang Kami</p>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <p>"Portal Warta Daerah telah menjadi sumber informasi utama bagi saya. Saya mengandalkan platform ini untuk mendapatkan berita terkini dan berkualitas."</p>
        <div class="testimonial-author">
          <div class="author-avatar">
            <img src="https://placehold.co/50x50" alt="Avatar">
          </div>
          <div class="author-info">
            <strong>Andi Suherman</strong>
            <span>Warga Sawerigadi</span>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <p>"Sebagai tokoh masyarakat, saya menemukan Portal Warta Daerah sebagai platform yang sempurna untuk berbagi pengalaman dengan masyarakat yang antusias."</p>
        <div class="testimonial-author">
          <div class="author-avatar">
            <img src="https://placehold.co/50x50" alt="Avatar">
          </div>
          <div class="author-info">
            <strong>Siti Aminah</strong>
            <span>Tokoh Masyarakat</span>
          </div>
        </div>
      </div>
    </div>
    <div class="testimonial-indicators">
      <span class="indicator active"></span>
      <span class="indicator"></span>
      <span class="indicator"></span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TestimonialsSection'
}
</script>

<style scoped>
.testimonials-section {
  background: #f9fafb;
  padding: 4rem 0;
  margin-bottom: 0;
}

.testimonials-header {
  text-align: center;
  max-width: 600px;
  margin: 0 auto 3rem;
}

.testimonials-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
}

.testimonials-header p {
  color: #6b7280;
  margin: 0;
}

.testimonials-grid {
  max-width: 1000px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
  padding: 0 2rem;
}

.testimonial-card {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.testimonial-card p {
  font-style: italic;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  color: #4b5563;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.author-avatar img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.author-info strong {
  display: block;
  color: #1f2937;
  font-weight: 600;
}

.author-info span {
  color: #6b7280;
  font-size: 0.9rem;
}

.testimonial-indicators {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 2rem;
}

.testimonial-indicators .indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #d1d5db;
  cursor: pointer;
}

.testimonial-indicators .indicator.active {
  background: #10b981;
}

/* Responsive Design */
@media (max-width: 768px) {
  .testimonials-grid {
    grid-template-columns: 1fr;
    padding: 0 1rem;
  }
}
</style>