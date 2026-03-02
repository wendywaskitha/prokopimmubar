<template>
  <div class="cta-section">
    <div class="cta-content">
      <h2>Sampaikan Aduan dan Aspirasi Anda</h2>
      <p>Bantu kami meningkatkan pelayanan publik dengan menyampaikan keluhan, saran, atau aspirasi Anda untuk pembangunan Muna Barat yang lebih baik.</p>
      <router-link to="/complaint" class="cta-button">Kirim Aduan Sekarang</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CtaSection'
}
</script>

<style scoped>
.cta-section {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  text-align: center;
  padding: 4rem 2rem;
}

.cta-content {
  max-width: 600px;
  margin: 0 auto;
}

.cta-content h2 {
  font-size: 2.2rem;
  font-weight: 700;
  margin: 0 0 1rem;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

.cta-content p {
  font-size: 1.1rem;
  opacity: 0.95;
  margin-bottom: 2rem;
  line-height: 1.6;
  text-shadow: 1px 1px 1px rgba(0,0,0,0.2);
}

.cta-button {
  display: inline-block;
  background: #1f2937;
  color: white;
  padding: 1rem 2rem;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.cta-button:hover {
  background: #111827;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}
</style>