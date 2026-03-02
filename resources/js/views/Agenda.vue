<template>
  <div class="agenda-page">
    <div class="page-header">
      <div class="container">
        <h1>Agenda Kegiatan</h1>
        <p>Jadwal kegiatan penting Pemerintah Kabupaten Muna Barat</p>
      </div>
    </div>

    <div class="container">
      <div class="content-wrapper">
        <main class="main-content">
          <div class="agenda-controls">
            <div class="filter-section">
              <div class="filter-group">
                <button
                  :class="['filter-btn', { active: activeFilter === 'all' }]"
                  @click="setFilter('all')"
                >
                  Semua Agenda
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
                  Sedang Berlangsung
                </button>
              </div>
            </div>

            <div class="search-section">
              <div class="search-box">
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Cari agenda..."
                  @input="debounceSearch"
                >
                <i class="fas fa-search"></i>
              </div>
            </div>
          </div>

          <div v-if="loading" class="loading-state">
            <div class="spinner"></div>
            <p>Memuat agenda...</p>
          </div>

          <div v-else-if="error" class="error-state">
            <p>{{ error }}</p>
            <button @click="loadAgendas" class="retry-btn">Coba Lagi</button>
          </div>

          <div v-else>
            <div v-if="agendas.length === 0" class="empty-state">
              <i class="fas fa-calendar-alt empty-icon"></i>
              <p>Tidak ada agenda yang tersedia.</p>
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
                    <div class="date-day">{{ formatDate(agenda.start_date).day }}</div>
                    <div class="date-month">{{ formatDate(agenda.start_date).month }}</div>
                    <div class="date-year">{{ formatDate(agenda.start_date).year }}</div>
                  </div>

                  <div class="agenda-content">
                    <div class="agenda-meta">
                      <span class="agenda-status" :class="getAgendaStatus(agenda)">
                        {{ getAgendaStatusText(agenda) }}
                      </span>
                      <span class="agenda-time" v-if="agenda.start_date && agenda.end_date">
                        <i class="far fa-clock"></i>
                        {{ formatTime(agenda.start_date) }} - {{ formatTime(agenda.end_date) }}
                      </span>
                    </div>

                    <h3 class="agenda-title">{{ agenda.title }}</h3>
                    <p class="agenda-description">{{ truncateText(agenda.description, 120) }}</p>

                    <div class="agenda-details">
                      <div class="agenda-location">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ agenda.location || 'Lokasi belum ditentukan' }}
                      </div>

                      <div class="agenda-creator">
                        <i class="fas fa-user"></i>
                        {{ agenda.creator?.name || 'Admin' }}
                      </div>
                    </div>
                  </div>
                </div>
              </router-link>
            </div>

            <div v-if="pagination.last_page > 1" class="pagination">
              <button
                :disabled="pagination.current_page === 1"
                @click="changePage(pagination.current_page - 1)"
                class="pagination-btn"
              >
                <i class="fas fa-chevron-left"></i>
              </button>

              <div class="pagination-pages">
                <button
                  v-for="page in getPageNumbers()"
                  :key="page"
                  :class="['pagination-page', { active: page === pagination.current_page }]"
                  @click="changePage(page)"
                  :disabled="page === '...'"
                >
                  {{ page }}
                </button>
              </div>

              <button
                :disabled="pagination.current_page === pagination.last_page"
                @click="changePage(pagination.current_page + 1)"
                class="pagination-btn"
              >
                <i class="fas fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </main>

        <aside class="sidebar">
          <div class="sidebar-widget">
            <h3><i class="fas fa-history"></i> Agenda Terkini</h3>
            <div v-if="recentAgendas.length > 0" class="recent-agendas">
              <div
                v-for="agenda in recentAgendas"
                :key="agenda.id"
                class="recent-agenda"
                @click="goToAgenda(agenda.id)"
              >
                <div class="recent-agenda-date">
                  <div class="day">{{ formatDate(agenda.start_date).day }}</div>
                  <div class="month">{{ formatDate(agenda.start_date).month }}</div>
                </div>
                <div class="recent-agenda-content">
                  <h4>{{ truncateText(agenda.title, 45) }}</h4>
                  <p>{{ truncateText(agenda.description, 60) }}</p>
                </div>
              </div>
            </div>
            <div v-else class="no-recent">
              <p>Tidak ada agenda terkini.</p>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'AgendaPage',
  data() {
    return {
      agendas: [],
      recentAgendas: [],
      loading: true,
      error: null,
      activeFilter: 'all',
      searchQuery: '',
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
      },
      searchTimeout: null
    };
  },
  mounted() {
    this.loadAgendas();
    this.loadRecentAgendas();
  },
  beforeUnmount() {
    // Clear timeout to prevent memory leaks
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
  },
  methods: {
    async loadAgendas() {
      this.loading = true;
      this.error = null;

      try {
        let response;
        const params = {
          page: this.pagination.current_page,
          per_page: this.pagination.per_page
        };

        if (this.searchQuery) {
          params.search = this.searchQuery;
        }

        if (this.activeFilter === 'upcoming') {
          response = await api.getUpcomingAgendas(params);
          this.agendas = response.data.data || [];
          // Untuk upcoming, kita tidak memiliki pagination dari API, jadi kita akan mensimulasinya
          this.pagination = {
            current_page: 1,
            last_page: 1,
            per_page: this.agendas.length,
            total: this.agendas.length
          };
        } else if (this.activeFilter === 'ongoing') {
          response = await api.getOngoingAgendas(params);
          this.agendas = response.data.data || [];
          // Untuk ongoing, kita tidak memiliki pagination dari API, jadi kita akan mensimulasinya
          this.pagination = {
            current_page: 1,
            last_page: 1,
            per_page: this.agendas.length,
            total: this.agendas.length
          };
        } else {
          response = await api.getAgendas(params);
          this.agendas = response.data.data || [];
          this.pagination = response.data.pagination || {
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0
          };
        }
      } catch (err) {
        this.error = 'Gagal memuat agenda. Silakan coba lagi.';
        console.error('Error loading agendas:', err);
      } finally {
        this.loading = false;
      }
    },

    async loadRecentAgendas() {
      try {
        const response = await api.getAgendas({ per_page: 5 });
        this.recentAgendas = response.data.data || [];
      } catch (err) {
        console.error('Error loading recent agendas:', err);
      }
    },

    setFilter(filter) {
      this.activeFilter = filter;
      this.pagination.current_page = 1;
      this.loadAgendas();
    },

    debounceSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.pagination.current_page = 1;
        this.loadAgendas();
      }, 500);
    },

    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page;
        this.loadAgendas();
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
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
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
      return text.length > length ? text.substring(0, length) + '...' : text;
    },

    goToAgenda(id) {
      if (this.$router) {
        this.$router.push(`/agenda/${id}`);
      }
    },

    getPageNumbers() {
      const currentPage = this.pagination.current_page;
      const lastPage = this.pagination.last_page;
      
      // Jika hanya ada 1 halaman, tidak perlu pagination
      if (lastPage <= 1) return [];
      
      const delta = 2; // Jumlah halaman di sekitar halaman saat ini
      const range = [];
      const rangeWithDots = [];

      // Tentukan range halaman yang akan ditampilkan
      for (let i = Math.max(1, currentPage - delta); i <= Math.min(lastPage, currentPage + delta); i++) {
        range.push(i);
      }

      // Tambahkan halaman pertama dan terakhir dengan dots jika perlu
      if (range[0] > 1) {
        rangeWithDots.push(1);
        if (range[0] > 2) {
          rangeWithDots.push('...');
        }
      }

      rangeWithDots.push(...range);

      if (range[range.length - 1] < lastPage) {
        if (range[range.length - 1] < lastPage - 1) {
          rangeWithDots.push('...');
        }
        rangeWithDots.push(lastPage);
      }

      return rangeWithDots;
    },
  }
};
</script>

<style scoped>
.agenda-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
  padding-bottom: 3rem;
}

.page-header {
  background: linear-gradient(120deg, #1e3a8a 0%, #2563eb 100%);
  color: white;
  padding: 2.5rem 0;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.page-header h1 {
  font-size: 2.2rem;
  margin-bottom: 0.75rem;
  font-weight: 700;
}

.page-header p {
  font-size: 1rem;
  max-width: 600px;
  margin: 0 auto;
  opacity: 0.9;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.25rem;
}

.content-wrapper {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
  padding: 1.5rem 0;
}

.main-content {
  background: white;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  padding: 1.75rem;
  transition: all 0.3s ease;
}

.agenda-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.75rem;
  flex-wrap: wrap;
  gap: 1rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #eef2f7;
}

.filter-section {
  flex: 1;
}

.filter-group {
  display: flex;
  gap: 0.5rem;
  padding: 0.5rem;
  background-color: #f1f5f9;
  border-radius: 50px;
  width: fit-content;
}

.filter-btn {
  padding: 0.5rem 1.25rem;
  border: none;
  border-radius: 50px;
  background: transparent;
  color: #64748b;
  font-weight: 500;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.filter-btn:hover {
  color: #1e293b;
}

.filter-btn.active {
  background: linear-gradient(120deg, #10b981 0%, #059669 100%);
  color: white;
  box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.search-section {
  flex: 1;
  max-width: 300px;
}

.search-box {
  position: relative;
  width: 100%;
}

.search-box input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #e2e8f0;
  border-radius: 50px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background-color: #f8fafc;
}

.search-box input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
  background-color: white;
}

.search-box i {
  position: absolute;
  left: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #64748b;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #cbd5e1;
}

.spinner {
  border: 3px solid rgba(0, 0, 0, 0.1);
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
  background: linear-gradient(120deg, #10b981 0%, #059669 100%);
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
  transform: translateY(-2px);
  box-shadow: 0 6px 8px rgba(16, 185, 129, 0.3);
}

.agenda-list {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.agenda-card-link {
  text-decoration: none;
  color: inherit;
  transition: transform 0.3s ease;
}

.agenda-card-link:hover {
  transform: translateY(-3px);
}

.agenda-card {
  display: flex;
  border: 1px solid #eef2f7;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.agenda-card:hover {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  border-color: #d1d5db;
}

.agenda-date {
  background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
  color: white;
  padding: 1.25rem;
  text-align: center;
  min-width: 100px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.date-day {
  font-size: 1.75rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.date-month {
  font-size: 0.9rem;
  text-transform: uppercase;
  margin: 0.1rem 0;
  font-weight: 500;
}

.date-year {
  font-size: 0.8rem;
  opacity: 0.9;
}

.agenda-content {
  padding: 1.25rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.agenda-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.8rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.agenda-status {
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.agenda-status.upcoming {
  background: linear-gradient(120deg, #dbeafe 0%, #bfdbfe 100%);
  color: #1d4ed8;
}

.agenda-status.ongoing {
  background: linear-gradient(120deg, #d1fae5 0%, #a7f3d0 100%);
  color: #047857;
}

.agenda-status.past {
  background: linear-gradient(120deg, #f3f4f6 0%, #e5e7eb 100%);
  color: #4b5563;
}

.agenda-time {
  font-size: 0.85rem;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.agenda-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.75rem;
  line-height: 1.3;
}

.agenda-description {
  color: #64748b;
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 1rem;
  flex-grow: 1;
}

.agenda-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.agenda-location, .agenda-creator {
  color: #64748b;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #eef2f7;
}

.pagination-btn {
  background: white;
  border: 1px solid #e2e8f0;
  color: #64748b;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.pagination-btn:hover:not(:disabled) {
  background: linear-gradient(120deg, #10b981 0%, #059669 100%);
  color: white;
  border-color: #10b981;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-pages {
  display: flex;
  gap: 0.5rem;
}

.pagination-page {
  background: white;
  border: 1px solid #e2e8f0;
  color: #64748b;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  font-weight: 500;
}

.pagination-page:hover:not(.active) {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
}

.pagination-page.active {
  background: linear-gradient(120deg, #10b981 0%, #059669 100%);
  color: white;
  border-color: #10b981;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

.pagination-page:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background-color: #f1f5f9;
  color: #94a3b8;
}

.sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.sidebar-widget {
  background: white;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  padding: 1.5rem;
  transition: all 0.3s ease;
}

.sidebar-widget h3 {
  font-size: 1.1rem;
  color: #1e293b;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #eef2f7;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.recent-agendas {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.recent-agenda {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #f1f5f9;
}

.recent-agenda:hover {
  background-color: #f8fafc;
  border-color: #e2e8f0;
  transform: translateX(3px);
}

.recent-agenda-date {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 50px;
}

.recent-agenda-date .day {
  font-size: 1.1rem;
  font-weight: 700;
  color: #10b981;
}

.recent-agenda-date .month {
  font-size: 0.7rem;
  text-transform: uppercase;
  color: #64748b;
}

.recent-agenda-content h4 {
  font-size: 0.9rem;
  color: #1e293b;
  margin-bottom: 0.25rem;
  line-height: 1.3;
  font-weight: 600;
}

.recent-agenda-content p {
  font-size: 0.8rem;
  color: #64748b;
  line-height: 1.4;
}

.no-recent {
  text-align: center;
  padding: 1rem;
  color: #64748b;
  font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 992px) {
  .content-wrapper {
    grid-template-columns: 1fr;
  }

  .search-section {
    max-width: 100%;
  }

  .agenda-controls {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-section, .search-section {
    width: 100%;
  }

  .filter-group {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .page-header {
    padding: 1.75rem 0;
  }

  .page-header h1 {
    font-size: 1.8rem;
  }

  .main-content {
    padding: 1.25rem;
  }

  .agenda-card {
    flex-direction: column;
  }

  .agenda-date {
    flex-direction: row;
    gap: 0.75rem;
    padding: 1rem;
    min-width: auto;
  }

  .date-day {
    font-size: 1.5rem;
  }

  .agenda-meta {
    flex-direction: column;
    align-items: flex-start;
  }

  .filter-group {
    flex-wrap: wrap;
    justify-content: center;
  }

  .agenda-controls {
    margin-bottom: 1.25rem;
  }
}

@media (max-width: 480px) {
  .filter-btn {
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
  }

  .agenda-title {
    font-size: 1.1rem;
  }

  .agenda-date {
    padding: 0.75rem;
  }

  .date-day {
    font-size: 1.25rem;
  }
}
</style>
