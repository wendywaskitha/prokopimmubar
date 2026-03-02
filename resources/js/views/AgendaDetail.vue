<template>
  <div class="agenda-detail-page">
    <div class="container">
      <div class="breadcrumb">
        <router-link to="/">Beranda</router-link>
        <span>/</span>
        <router-link to="/agenda">Agenda</router-link>
        <span>/</span>
        <span>{{ agenda?.title || 'Detail Agenda' }}</span>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Memuat detail agenda...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="loadAgenda" class="retry-btn">Coba Lagi</button>
      </div>

      <div v-else-if="agenda" class="agenda-detail">
        <div class="agenda-header">
          <h1 class="agenda-title">{{ agenda.title }}</h1>

          <div class="agenda-meta">
            <div class="meta-item">
              <i class="far fa-calendar-alt"></i>
              <span v-if="agenda.start_date && agenda.end_date">{{ formatDate(agenda.start_date).full }} - {{ formatDate(agenda.end_date).full }}</span>
              <span v-else>Tanggal tidak tersedia</span>
            </div>
            
            <div class="meta-item">
              <i class="far fa-clock"></i>
              <span v-if="agenda.start_date && agenda.end_date">{{ formatTime(agenda.start_date) }} - {{ formatTime(agenda.end_date) }}</span>
              <span v-else>Waktu tidak tersedia</span>
            </div>
            
            <div class="meta-item">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ agenda.location || 'Lokasi belum ditentukan' }}</span>
            </div>
            
            <div class="meta-item">
              <i class="fas fa-user"></i>
              <span>Diposting oleh: {{ agenda.creator?.name || 'Admin' }}</span>
            </div>
          </div>

          <div class="agenda-status" :class="getAgendaStatus(agenda)">
            {{ getAgendaStatusText(agenda) }}
          </div>
        </div>

        <div class="agenda-content">
          <div class="content-section">
            <h2>Deskripsi Kegiatan</h2>
            <div class="description" v-html="formatDescription(agenda.description)"></div>
          </div>

          <div class="content-section">
            <h2>Detail Waktu</h2>
            <div class="time-details">
              <div class="time-item">
                <strong>Tanggal Mulai:</strong>
                <span v-if="agenda.start_date">{{ formatDate(agenda.start_date).full }} pukul {{ formatTime(agenda.start_date) }}</span>
                <span v-else>Tanggal mulai tidak tersedia</span>
              </div>
              <div class="time-item">
                <strong>Tanggal Selesai:</strong>
                <span v-if="agenda.end_date">{{ formatDate(agenda.end_date).full }} pukul {{ formatTime(agenda.end_date) }}</span>
                <span v-else>Tanggal selesai tidak tersedia</span>
              </div>
              <div class="time-item">
                <strong>Durasi:</strong>
                <span v-if="agenda.start_date && agenda.end_date">{{ formatDuration(agenda.start_date, agenda.end_date) }}</span>
                <span v-else>Durasi tidak tersedia</span>
              </div>
            </div>
          </div>

          <div class="content-section" v-if="agenda.location">
            <h2>Lokasi</h2>
            <div class="location-details">
              <p>{{ agenda.location }}</p>
              <!-- In the future, we could add a map component here -->
            </div>
          </div>
        </div>
      </div>

      <div v-else class="not-found">
        <h2>Agenda Tidak Ditemukan</h2>
        <p>Agenda yang Anda cari tidak tersedia atau tidak ditemukan.</p>
        <router-link to="/agenda" class="back-link">Kembali ke Daftar Agenda</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'AgendaDetail',
  data() {
    return {
      agenda: null,
      loading: true,
      error: null
    };
  },
  async mounted() {
    await this.loadAgenda();
  },
  watch: {
    '$route.params.id': {
      handler() {
        this.loadAgenda();
      },
      immediate: false
    }
  },
  methods: {
    async loadAgenda() {
      this.loading = true;
      this.error = null;

      try {
        const id = this.$route.params.id;
        if (!id) {
          throw new Error('ID agenda tidak valid');
        }

        const response = await api.getAgendaById(id);
        this.agenda = response.data.data;
      } catch (err) {
        if (err.response && err.response.status === 404) {
          this.error = 'Agenda tidak ditemukan.';
        } else {
          this.error = 'Gagal memuat detail agenda. Silakan coba lagi.';
        }
        console.error('Error loading agenda:', err);
      } finally {
        this.loading = false;
      }
    },

    formatDate(dateString) {
      if (!dateString) return { day: '', month: '', year: '', full: '' };

      // Parse tanggal dari format "YYYY-MM-DD HH:MM:SS"
      let date;
      if (typeof dateString === 'string' && dateString.includes(' ')) {
        // Format: "2025-09-16 09:00:00"
        const [datePart, timePart] = dateString.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hour, minute, second] = timePart.split(':');
        date = new Date(year, month - 1, day, hour, minute, second);
      } else {
        // Format standar lainnya
        date = new Date(dateString);
      }

      if (isNaN(date.getTime())) {
        return { day: '', month: '', year: '', full: '' };
      }

      const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
      ];

      return {
        day: date.getDate(),
        month: months[date.getMonth()],
        year: date.getFullYear(),
        full: `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`
      };
    },

    formatTime(dateString) {
      if (!dateString) return '';

      // Parse waktu dari format "YYYY-MM-DD HH:MM:SS"
      let date;
      if (typeof dateString === 'string' && dateString.includes(' ')) {
        // Format: "2025-09-16 09:00:00"
        const [datePart, timePart] = dateString.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hour, minute, second] = timePart.split(':');
        date = new Date(year, month - 1, day, hour, minute, second);
      } else {
        // Format standar lainnya
        date = new Date(dateString);
      }

      if (isNaN(date.getTime())) {
        return '';
      }

      return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
      });
    },

    formatDuration(startDate, endDate) {
      if (!startDate || !endDate) return 'Durasi tidak tersedia';

      // Parse tanggal dari format "YYYY-MM-DD HH:MM:SS"
      let start, end;
      
      // Parse start date
      if (typeof startDate === 'string' && startDate.includes(' ')) {
        const [datePart, timePart] = startDate.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hour, minute, second] = timePart.split(':');
        start = new Date(year, month - 1, day, hour, minute, second);
      } else {
        start = new Date(startDate);
      }
      
      // Parse end date
      if (typeof endDate === 'string' && endDate.includes(' ')) {
        const [datePart, timePart] = endDate.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hour, minute, second] = timePart.split(':');
        end = new Date(year, month - 1, day, hour, minute, second);
      } else {
        end = new Date(endDate);
      }

      if (isNaN(start.getTime()) || isNaN(end.getTime())) {
        return 'Durasi tidak tersedia';
      }

      const diffMs = end - start;
      const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
      const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

      let duration = '';
      if (diffDays > 0) {
        duration += `${diffDays} hari `;
      }
      if (diffHours > 0) {
        duration += `${diffHours} jam `;
      }
      if (diffMinutes > 0) {
        duration += `${diffMinutes} menit`;
      }

      return duration.trim() || 'Kurang dari 1 menit';
    },

    getAgendaStatus(agenda) {
      if (!agenda || !agenda.start_date || !agenda.end_date) return 'unknown';

      const now = new Date();
      const startDate = new Date(agenda.start_date);
      const endDate = new Date(agenda.end_date);

      if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) return 'unknown';

      if (startDate > now) {
        return 'upcoming';
      } else if (startDate <= now && endDate >= now) {
        return 'ongoing';
      }
      return 'past';
    },

    getAgendaStatusText(agenda) {
      const status = this.getAgendaStatus(agenda);
      switch (status) {
        case 'upcoming':
          return 'Akan Datang';
        case 'ongoing':
          return 'Sedang Berlangsung';
        case 'past':
          return 'Telah Selesai';
        default:
          return '';
      }
    },

    formatDescription(description) {
      if (!description) return '';
      // Sanitize HTML to prevent XSS attacks
      return description.replace(/\n/g, '<br>').replace(/<script.*?>.*?<\/script>/gi, '');
    }
  }
};
</script>

<style scoped>
.agenda-detail-page {
  min-height: 100vh;
  background-color: #f1f5f9;
  padding: 2rem 0;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

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
  color: #64748b;
}

.loading-state, .error-state, .not-found {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-left-color: #10b981;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.retry-btn {
  background-color: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  margin-top: 1rem;
  transition: background-color 0.3s ease;
}

.retry-btn:hover {
  background-color: #059669;
}

.agenda-detail {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.agenda-header {
  padding: 2rem;
  background-color: #1e293b;
  color: white;
  position: relative;
}

.agenda-title {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  line-height: 1.3;
}

.agenda-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.95rem;
}

.agenda-status {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
}

.agenda-status.upcoming {
  background-color: #dbeafe;
  color: #2563eb;
}

.agenda-status.ongoing {
  background-color: #d1fae5;
  color: #059669;
}

.agenda-status.past {
  background-color: #f3f4f6;
  color: #6b7280;
}

.agenda-status.unknown {
  background-color: #f1f5f9;
  color: #64748b;
}

.agenda-content {
  padding: 2rem;
}

.content-section {
  margin-bottom: 2rem;
}

.content-section h2 {
  font-size: 1.5rem;
  color: #1e293b;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.description {
  color: #334155;
  line-height: 1.7;
  font-size: 1.05rem;
}

.time-details {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.time-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.time-item:last-child {
  border-bottom: none;
}

.time-item strong {
  color: #1e293b;
  min-width: 150px;
}

.location-details p {
  color: #334155;
  line-height: 1.6;
  font-size: 1.05rem;
}

.not-found {
  text-align: center;
}

.not-found h2 {
  color: #1e293b;
  margin-bottom: 1rem;
}

.not-found p {
  color: #64748b;
  margin-bottom: 1.5rem;
}

.back-link {
  display: inline-block;
  background-color: #10b981;
  color: white;
  text-decoration: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.back-link:hover {
  background-color: #059669;
}

/* Responsive Design */
@media (max-width: 768px) {
  .agenda-detail-page {
    padding: 1rem 0;
  }

  .agenda-header {
    padding: 1.5rem;
  }

  .agenda-title {
    font-size: 1.75rem;
  }

  .agenda-meta {
    grid-template-columns: 1fr;
  }

  .agenda-content {
    padding: 1.5rem;
  }

  .content-section h2 {
    font-size: 1.3rem;
  }

  .time-item {
    flex-direction: column;
    gap: 0.25rem;
  }

  .time-item strong {
    min-width: auto;
  }
}
</style>
