<template>
  <section class="agenda-section">
    <div class="container">
      <div class="section-header">
        <div class="header-content">
          <h2 class="section-title">Agenda Kegiatan</h2>
          <p class="section-subtitle">Jadwal kegiatan penting Pemerintah Kabupaten Muna Barat</p>
        </div>
        <div class="header-actions">
          <router-link to="/agenda" class="view-all-link">
            Lihat Semua
            <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>
      </div>

      <div class="agenda-controls">
        <div class="filter-group">
          <button
            :class="['filter-btn', { active: activeFilter === 'all' }]"
            @click="setFilter('all')"
          >
            Semua
          </button>
          <button
            :class="['filter-btn', { active: activeFilter === 'upcoming' }]"
            @click="setFilter('upcoming')"
          >
            Akan Datang
          </button>
          <button
            :class="['filter-btn', { active: activeFilter === 'ongoing' }]"
            @click="setFilter('ongoing')"
          >
            Berlangsung
          </button>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner-container">
          <div class="spinner"></div>
          <p>Memuat agenda...</p>
        </div>
      </div>

      <div v-else-if="error" class="error-state">
        <div class="error-content">
          <i class="fas fa-exclamation-circle error-icon"></i>
          <p>{{ error }}</p>
          <button @click="loadAgendas" class="retry-btn">Coba Lagi</button>
        </div>
      </div>

      <div v-else-if="agendas.length === 0" class="empty-state">
        <div class="empty-content">
          <i class="fas fa-calendar-alt empty-icon"></i>
          <p>Tidak ada agenda yang tersedia.</p>
        </div>
      </div>

      <div v-else class="agenda-list">
        <router-link
          v-for="agenda in agendas" 
          :key="agenda.id" 
          :to="`/agenda/${agenda.id}`"
          class="agenda-card-link"
        >
          <div class="agenda-card">
            <div class="agenda-date">
              <div class="date-container">
                <div class="date-day">{{ formatDate(agenda.start_date).day }}</div>
                <div class="date-month">{{ formatDate(agenda.start_date).month }}</div>
              </div>
              <div class="date-year">{{ formatDate(agenda.start_date).year }}</div>
            </div>
            
            <div class="agenda-content">
              <div class="agenda-header">
                <span class="agenda-status" :class="getAgendaStatus(agenda)">
                  {{ getAgendaStatusText(agenda) }}
                </span>
                <span class="agenda-time" v-if="agenda.start_date && agenda.end_date">
                  <i class="far fa-clock"></i>
                  {{ formatTime(agenda.start_date) }} - {{ formatTime(agenda.end_date) }}
                </span>
              </div>
              
              <h3 class="agenda-title">{{ truncateText(agenda.title, 60) }}</h3>
              <p class="agenda-description">{{ truncateText(agenda.description, 100) }}</p>
              
              <div class="agenda-footer">
                <div class="agenda-location">
                  <i class="fas fa-map-marker-alt"></i>
                  {{ truncateText(agenda.location || 'Lokasi belum ditentukan', 30) }}
                </div>
              </div>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script>
import api from '../services/api';

export default {
  name: 'Agenda',
  data() {
    return {
      agendas: [],
      loading: true,
      error: null,
      activeFilter: 'all'
    };
  },
  async mounted() {
    await this.loadAgendas();
  },
  methods: {
    async loadAgendas() {
      this.loading = true;
      this.error = null;
      
      try {
        let response;
        
        if (this.activeFilter === 'upcoming') {
          response = await api.getUpcomingAgendas();
        } else if (this.activeFilter === 'ongoing') {
          response = await api.getOngoingAgendas();
        } else {
          // Memuat maksimal 5 agenda untuk tampilan di halaman utama
          response = await api.getAgendas({ per_page: 5 });
        }
        
        this.agendas = response.data.data || [];
      } catch (err) {
        this.error = 'Gagal memuat agenda. Silakan coba lagi.';
        console.error('Error loading agendas:', err);
      } finally {
        this.loading = false;
      }
    },

    setFilter(filter) {
      if (this.activeFilter !== filter) {
        this.activeFilter = filter;
        this.loadAgendas();
      }
    },

    formatDate(dateString) {
      if (!dateString) return { day: '', month: '', year: '' };

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
        return { day: '', month: '', year: '' };
      }

      const months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
      ];

      return {
        day: date.getDate(),
        month: months[date.getMonth()],
        year: date.getFullYear()
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

    getAgendaStatus(agenda) {
      if (!agenda || !agenda.start_date || !agenda.end_date) return 'unknown';

      // Parse tanggal dengan penanganan error yang lebih baik
      const startDate = new Date(agenda.start_date);
      const endDate = new Date(agenda.end_date);

      if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) return 'unknown';

      const now = new Date();
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
          return 'Status Tidak Diketahui';
      }
    },

    truncateText(text, length) {
      if (!text) return '';
      if (typeof text !== 'string') return '';
      return text.length > length ? text.substring(0, length) + '...' : text;
    }
  }
};
</script>

<style scoped>
.agenda-section {
  padding: 3rem 0;
  background-color: #f8fafc;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-content {
  flex: 1;
}

.section-title {
  font-size: 2rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 0.5rem;
  position: relative;
  display: inline-block;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 4px;
  background-color: #10b981;
  border-radius: 2px;
}

.section-subtitle {
  font-size: 1rem;
  color: #64748b;
  max-width: 600px;
  margin: 0;
}

.header-actions .view-all-link {
  background-color: #10b981;
  color: white;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.header-actions .view-all-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3);
  background-color: #059669;
}

.agenda-controls {
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
}

.filter-group {
  display: flex;
  gap: 0.5rem;
  padding: 0.5rem;
  background-color: #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.filter-btn {
  padding: 0.6rem 1.25rem;
  border: none;
  border-radius: 12px;
  background: transparent;
  color: #64748b;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  color: #1e293b;
}

.filter-btn.active {
  background-color: #10b981;
  color: white;
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

.spinner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-left-color: #10b981;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

.error-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.error-icon {
  font-size: 3rem;
  color: #ef4444;
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
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.retry-btn:hover {
  background-color: #059669;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(16, 185, 129, 0.3);
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
}

.empty-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.empty-icon {
  font-size: 3rem;
  color: #cbd5e1;
}

.agenda-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.agenda-card-link {
  text-decoration: none;
  color: inherit;
  transition: transform 0.3s ease;
}

.agenda-card-link:hover {
  transform: translateY(-5px);
}

.agenda-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  display: flex;
  transition: all 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.agenda-card:hover {
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
  border-color: rgba(0, 0, 0, 0.1);
}

.agenda-date {
  background-color: #1e293b;
  color: white;
  padding: 1.5rem;
  text-align: center;
  min-width: 110px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.date-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 0.5rem;
}

.date-day {
  font-size: 2.2rem;
  font-weight: 800;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.date-month {
  font-size: 1rem;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.date-year {
  font-size: 0.85rem;
  opacity: 0.9;
  font-weight: 500;
}

.agenda-content {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.agenda-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.agenda-status {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

.agenda-time {
  font-size: 0.8rem;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  background: #f8fafc;
  padding: 0.3rem 0.75rem;
  border-radius: 20px;
}

.agenda-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.agenda-description {
  color: #64748b;
  font-size: 0.9rem;
  line-height: 1.6;
  margin-bottom: 1rem;
  flex-grow: 1;
}

.agenda-footer {
  margin-top: auto;
}

.agenda-location {
  color: #64748b;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .agenda-section {
    padding: 2rem 0;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .section-title {
    font-size: 1.75rem;
  }

  .agenda-list {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .agenda-card {
    flex-direction: row;
  }

  .agenda-date {
    flex-direction: row;
    gap: 0.75rem;
    padding: 1.25rem;
    min-width: auto;
    width: 130px;
  }

  .date-container {
    flex-direction: row;
    gap: 0.5rem;
    margin-bottom: 0;
  }

  .date-day {
    font-size: 1.5rem;
    margin-bottom: 0;
  }

  .date-month {
    font-size: 0.8rem;
  }

  .agenda-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .filter-group {
    flex-wrap: wrap;
    justify-content: center;
    border-radius: 12px;
  }

  .filter-btn {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 1rem;
  }

  .agenda-card {
    flex-direction: column;
  }

  .agenda-date {
    width: 100%;
    padding: 1rem;
  }

  .date-container {
    flex-direction: row;
    gap: 0.5rem;
  }

  .agenda-list {
    gap: 1.25rem;
  }
}
</style>
