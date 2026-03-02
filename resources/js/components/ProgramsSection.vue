<template>
  <div class="programs-section">
    <div class="section-header">
      <h2>Program Kerja</h2>
      <div class="section-divider"></div>
      <p class="section-description">Program-program kerja kepala daerah Kabupaten Muna Barat</p>
    </div>

    <div class="loading" v-if="loading">
      <div class="loading-spinner"></div>
      <p>Memuat program kerja...</p>
    </div>

    <div class="error" v-else-if="error">
      <i class="error-icon">⚠️</i>
      <p>{{ error }}</p>
    </div>

    <div class="programs-content" v-else>
      <div class="programs-grid">
        <router-link
          v-for="program in programs"
          :key="program.id"
          :to="`/programs/${program.id}`"
          class="program-card-link"
        >
          <div
            class="program-card"
            :class="getPriorityClass(program.priority)"
          >
            <div class="program-header">
              <span class="program-category">{{ program.category }}</span>
              <span class="program-status" :class="getStatusClass(program.status)">
                {{ getStatusText(program.status) }}
              </span>
            </div>
            <h3 class="program-title">{{ program.title }}</h3>
            <p class="program-description">{{ program.description }}</p>
            <div class="program-meta">
              <div class="meta-item">
                <i class="meta-icon">📅</i>
                <span>{{ formatDate(program.start_date) }} - {{ formatDate(program.end_date) }}</span>
              </div>
              <div class="meta-item">
                <i class="meta-icon">👤</i>
                <span>{{ program.author?.name || 'Admin' }}</span>
              </div>
            </div>
            <div class="progress-container" v-if="program.progress !== undefined">
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: program.progress + '%' }"></div>
              </div>
              <span class="progress-text">{{ program.progress }}% Complete</span>
            </div>
          </div>
        </router-link>
      </div>

      <div class="view-all-container">
        <router-link to="/programs" class="view-all-button">Lihat Semua Program</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  name: 'ProgramsSection',
  data() {
    return {
      programs: [],
      loading: false,
      error: null
    }
  },
  mounted() {
    this.fetchPrograms();
  },
  methods: {
    async fetchPrograms() {
      this.loading = true;
      this.error = null;
      try {
        const response = await api.getWorkPrograms({ per_page: 4 });
        this.programs = response.data.data;
      } catch (err) {
        this.error = 'Gagal memuat program kerja. Silakan coba lagi.';
        console.error('Error fetching work programs:', err);
      } finally {
        this.loading = false;
      }
    },
    getPriorityClass(priority) {
      const priorityClasses = {
        1: 'low-priority',
        2: 'medium-priority',
        3: 'high-priority'
      };
      return priorityClasses[priority] || '';
    },
    getStatusClass(status) {
      const statusClasses = {
        'planned': 'status-planned',
        'in_progress': 'status-in-progress',
        'completed': 'status-completed',
        'delayed': 'status-delayed'
      };
      return statusClasses[status] || '';
    },
    getStatusText(status) {
      const statusTexts = {
        'planned': 'Direncanakan',
        'in_progress': 'Dalam Proses',
        'completed': 'Selesai',
        'delayed': 'Terlambat'
      };
      return statusTexts[status] || status;
    },
    formatDate(dateString) {
      if (!dateString) return 'Tidak ditentukan';
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    }
  }
}
</script>

<style scoped>
.programs-section {
  max-width: 1200px;
  margin: 0 auto 4rem;
  padding: 3rem 0;
}

.section-header {
  text-align: center;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
}

.section-divider {
  width: 80px;
  height: 4px;
  background: linear-gradient(135deg, #10b981, #059669);
  margin: 1rem auto 2rem;
  border-radius: 2px;
}

.section-description {
  color: #6b7280;
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto;
}

.programs-content {
  margin-top: 2rem;
}

.programs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.program-card-link {
  text-decoration: none;
  color: inherit;
}

.program-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  padding: 1.5rem;
  border-left: 4px solid #10b981;
  cursor: pointer;
}

.program-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.program-card.high-priority {
  border-left-color: #ef4444;
}

.program-card.medium-priority {
  border-left-color: #f59e0b;
}

.program-card.low-priority {
  border-left-color: #10b981;
}

.program-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.program-category {
  background: #f3f4f6;
  color: #4b5563;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.program-status {
  font-size: 0.8rem;
  font-weight: 500;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
}

.status-planned {
  background: #dbeafe;
  color: #1d4ed8;
}

.status-in-progress {
  background: #ffedd5;
  color: #c2410c;
}

.status-completed {
  background: #dcfce7;
  color: #15803d;
}

.status-delayed {
  background: #fee2e2;
  color: #b91c1c;
}

.program-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
}

.program-description {
  color: #6b7280;
  margin: 0 0 1.5rem;
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.program-meta {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  color: #4b5563;
}

.meta-icon {
  font-size: 1rem;
}

.progress-container {
  margin-top: 1rem;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: #10b981;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.8rem;
  color: #6b7280;
  text-align: right;
  margin-top: 0.25rem;
}

.view-all-container {
  text-align: center;
  margin-top: 3rem;
}

.view-all-button {
  display: inline-block;
  background: #10b981;
  color: white;
  padding: 1rem 2rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
}

.view-all-button:hover {
  background: #059669;
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
}

/* Loading & Error States */
.loading,
.error {
  text-align: center;
  padding: 3rem 2rem;
  border-radius: 15px;
  margin: 2rem 0;
}

.loading {
  background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
  color: #047857;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #d1fae5;
  border-top: 4px solid #10b981;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error {
  background: linear-gradient(135deg, #fef2f2, #fee2e2);
  color: #dc2626;
}

.error-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .programs-section {
    padding: 0 1rem;
  }

  .programs-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .section-header h2 {
    font-size: 1.75rem;
  }

  .section-description {
    font-size: 1rem;
  }

  .program-card {
    border-left: none;
    border-top: 4px solid #10b981;
  }

  .program-card.high-priority {
    border-top-color: #ef4444;
  }

  .program-card.medium-priority {
    border-top-color: #f59e0b;
  }

  .program-card.low-priority {
    border-top-color: #10b981;
  }
}

@media (max-width: 480px) {
  .programs-section {
    padding: 0 0.75rem;
    margin-bottom: 2.5rem;
  }

  .section-header {
    margin-bottom: 1.5rem;
  }

  .section-header h2 {
    font-size: 1.5rem;
  }

  .section-divider {
    margin: 0.75rem auto 1.5rem;
  }

  .section-description {
    font-size: 0.9rem;
  }

  .programs-content {
    margin-top: 1.5rem;
  }

  .programs-grid {
    gap: 1.25rem;
  }

  .program-card {
    padding: 1.25rem;
  }

  .program-title {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
  }

  .program-description {
    font-size: 0.9rem;
    margin-bottom: 1.25rem;
  }

  .program-meta {
    margin-bottom: 1.25rem;
  }

  .meta-item {
    font-size: 0.85rem;
  }

  .progress-text {
    font-size: 0.75rem;
  }

  .view-all-container {
    margin-top: 2rem;
  }

  .view-all-button {
    padding: 0.85rem 1.5rem;
    font-size: 1rem;
  }
}
</style>
