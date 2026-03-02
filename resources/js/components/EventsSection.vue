<template>
  <div class="events-section">
    <div class="event-card">
      <div class="event-image">
        <img src="https://placehold.co/300x200" alt="Event">
      </div>
      <div class="event-content">
        <span class="event-label">Acara Mendatang</span>
        <h2>Festival Budaya Muna Barat #132 di Sawerigadi</h2>
        <div class="event-countdown">
          <div class="countdown-item">
            <span class="countdown-number">09</span>
            <span class="countdown-label">Hari</span>
          </div>
          <div class="countdown-separator">:</div>
          <div class="countdown-item">
            <span class="countdown-number">14</span>
            <span class="countdown-label">Jam</span>
          </div>
          <div class="countdown-separator">:</div>
          <div class="countdown-item">
            <span class="countdown-number">23</span>
            <span class="countdown-label">Menit</span>
          </div>
          <div class="countdown-separator">:</div>
          <div class="countdown-item">
            <span class="countdown-number">01</span>
            <span class="countdown-label">Detik</span>
          </div>
        </div>
        <router-link to="/complaint" class="event-register-btn">Daftar Sekarang</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EventsSection'
}
</script>

<style scoped>
.events-section {
  max-width: 1200px;
  margin: 0 auto 4rem;
  padding: 0 2rem;
}

.event-card {
  background: #1f2937;
  border-radius: 20px;
  overflow: hidden;
  display: grid;
  grid-template-columns: 300px 1fr;
  color: white;
}

.event-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.event-content {
  padding: 3rem;
}

.event-label {
  color: #10b981;
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.event-content h2 {
  font-size: 2rem;
  margin: 0.5rem 0 2rem;
  line-height: 1.2;
}

.event-countdown {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.countdown-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.countdown-number {
  font-size: 2rem;
  font-weight: 700;
  color: #10b981;
}

.countdown-label {
  font-size: 0.8rem;
  color: #9ca3af;
}

.countdown-separator {
  font-size: 1.5rem;
  color: #6b7280;
}

.event-register-btn {
  background: #10b981;
  color: white;
  padding: 1rem 2rem;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.3s ease;
  display: inline-block;
  width: fit-content;
}

.event-register-btn:hover {
  background: #059669;
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .event-card {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .event-content {
    padding: 2rem;
  }
  
  .event-countdown {
    flex-wrap: wrap;
  }
}
</style>