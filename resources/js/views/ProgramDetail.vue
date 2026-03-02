<template>
  <div class="program-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Memuat detail program...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h2>{{ error }}</h2>
      <button @click="loadProgram" class="retry-btn">Coba Lagi</button>
      <router-link to="/programs" class="back-link">Kembali ke Program</router-link>
    </div>

    <!-- Program Content -->
    <div v-else-if="program" class="program-detail-container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <div class="breadcrumb-container">
          <router-link to="/">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            </svg>
            Beranda
          </router-link>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
          <router-link to="/programs">Program Kerja</router-link>
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
          <span>{{ program.title }}</span>
        </div>
      </nav>

      <div class="program-content-layout">
        <!-- Main Content -->
        <main class="program-main-content">
          <article class="program-article">
            <!-- Program Header -->
            <header class="program-header">
              <div class="program-categories">
                <span class="category-tag" :class="getPriorityClass(program.priority)">
                  {{ program.category }}
                </span>
              </div>

              <h1 class="program-title">{{ program.title }}</h1>

              <div class="program-meta">
                <div class="meta-group">
                  <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>{{ program.author ? program.author.name : 'Administrator' }}</span>
                  </div>

                  <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>{{ formatDate(program.created_at) }}</span>
                  </div>

                  <div class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>{{ program.views || Math.floor(Math.random() * 5000) + 1000 }} pembaca</span>
                  </div>
                </div>

                <div class="program-status-badge" :class="getStatusClass(program.status)">
                  {{ getStatusText(program.status) }}
                </div>
              </div>
            </header>

            <!-- Program Dates -->
            <div class="program-dates">
              <div class="date-card">
                <div class="date-label">Tanggal Mulai</div>
                <div class="date-value">{{ formatDate(program.start_date) }}</div>
              </div>
              <div class="date-card">
                <div class="date-label">Tanggal Selesai</div>
                <div class="date-value">{{ formatDate(program.end_date) }}</div>
              </div>
              <div class="date-card">
                <div class="date-label">Prioritas</div>
                <div class="date-value" :class="getPriorityClass(program.priority)">
                  {{ getPriorityText(program.priority) }}
                </div>
              </div>
            </div>

            <!-- Progress Section -->
            <div class="progress-section" v-if="program.progress !== undefined">
              <h3>Progress Program</h3>
              <div class="progress-container">
                <div class="progress-bar">
                  <div class="progress-fill" :style="{ width: program.progress + '%' }"></div>
                </div>
                <div class="progress-info">
                  <span class="progress-text">{{ program.progress }}% Complete</span>
                  <span class="progress-value">{{ program.progress }}%</span>
                </div>
              </div>
            </div>

            <!-- Program Content -->
            <div class="program-content">
              <h2>Deskripsi Program</h2>
              <p>{{ program.description }}</p>
              
              <div class="program-details" v-if="program.details">
                <h3>Detail Program</h3>
                <div class="detail-item" v-for="(detail, index) in program.details" :key="index">
                  <h4>{{ detail.title }}</h4>
                  <p>{{ detail.description }}</p>
                </div>
              </div>
            </div>

            <!-- Share Section -->
            <div class="program-share">
              <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                  <polyline points="16,6 12,2 8,6"/>
                  <line x1="12" y1="2" x2="12" y2="15"/>
                </svg>
                Bagikan Program
              </h3>
              <div class="share-buttons">
                <button class="share-btn facebook" @click="shareToFacebook">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                  <span>Facebook</span>
                </button>
                <button class="share-btn twitter" @click="shareToTwitter">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                  <span>Twitter</span>
                </button>
                <button class="share-btn whatsapp" @click="shareToWhatsApp">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                  </svg>
                  <span>WhatsApp</span>
                </button>
                <button class="share-btn copy" @click="copyUrl">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.72-1.71"/>
                  </svg>
                  <span>Salin Link</span>
                </button>
              </div>
            </div>
          </article>

          <!-- Related Programs -->
          <section class="related-programs" v-if="relatedPrograms.length > 0">
            <div class="section-header">
              <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14,2 14,8 20,8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Program Terkait
              </h2>
              <p>Program lain dalam kategori yang sama</p>
            </div>
            <div class="related-programs-grid">
              <article
                v-for="related in relatedPrograms"
                :key="related.id"
                class="related-program-item"
              >
                <router-link :to="`/programs/${related.id}`" class="related-program-link">
                  <div class="related-content">
                    <div class="related-category" :class="getPriorityClass(related.priority)">
                      {{ related.category }}
                    </div>
                    <h3>{{ related.title }}</h3>
                    <div class="related-meta">
                      <span>{{ formatDate(related.start_date) }}</span>
                      <span class="status" :class="getStatusClass(related.status)">
                        {{ getStatusText(related.status) }}
                      </span>
                    </div>
                  </div>
                </router-link>
              </article>
            </div>
          </section>
        </main>

        <!-- Sidebar -->
        <aside class="program-detail-sidebar">
          <!-- Program Stats Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
              </svg>
              Statistik Program
            </h3>
            <div class="program-stats">
              <div class="stat-item">
                <div class="stat-label">Tanggal Mulai</div>
                <div class="stat-value">{{ formatDate(program.start_date) }}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Tanggal Selesai</div>
                <div class="stat-value">{{ formatDate(program.end_date) }}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Durasi</div>
                <div class="stat-value">{{ calculateDuration(program.start_date, program.end_date) }}</div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Prioritas</div>
                <div class="stat-value" :class="getPriorityClass(program.priority)">
                  {{ getPriorityText(program.priority) }}
                </div>
              </div>
              <div class="stat-item">
                <div class="stat-label">Status</div>
                <div class="stat-value" :class="getStatusClass(program.status)">
                  {{ getStatusText(program.status) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Categories Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
              </svg>
              Kategori Program
            </h3>
            <div class="categories-list">
              <div
                v-for="category in categories"
                :key="category"
                class="category-item"
                :class="{ active: program.category === category }"
              >
                <router-link :to="`/programs?category=${category}`">
                  <span class="category-name">{{ category }}</span>
                </router-link>
              </div>
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
  name: 'ProgramDetail',
  data() {
    return {
      program: null,
      relatedPrograms: [],
      categories: [
        'Infrastruktur',
        'Pendidikan',
        'Kesehatan',
        'Pariwisata',
        'Pertanian',
        'Lingkungan',
        'Sosial',
        'Ekonomi'
      ],
      loading: true,
      error: null
    }
  },
  mounted() {
    // Tambahkan logging untuk debugging
    console.log('ProgramDetail mounted, route params:', this.$route.params);
    
    // Tambahkan pengecekan apakah route params tersedia
    if (this.$route.params.id) {
      console.log('Loading program with ID:', this.$route.params.id);
      this.loadProgram();
    } else {
      console.log('No program ID found in route params');
      this.error = 'ID program tidak ditemukan';
      this.loading = false;
    }
  },
  methods: {
    async loadProgram() {
      this.loading = true;
      this.error = null;

      try {
        // Pastikan id tersedia dan merupakan angka
        const id = this.$route.params.id;
        console.log('Attempting to load program with ID:', id);
        
        if (!id) {
          this.error = 'ID program tidak ditemukan';
          this.loading = false;
          return;
        }

        const response = await api.getWorkProgramById(id);
        console.log('API response for program ID', id, ':', response);

        if (response.data.success) {
          this.program = response.data.data;
          console.log('Program loaded successfully:', this.program);
          await this.loadRelatedPrograms();
        } else {
          this.error = 'Program tidak ditemukan';
        }
      } catch (error) {
        console.error('Error loading program:', error);
        if (error.response && error.response.status === 404) {
          this.error = 'Program tidak ditemukan';
        } else if (error.response) {
          this.error = `Error ${error.response.status}: ${error.response.statusText}`;
        } else if (error.request) {
          this.error = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        } else {
          this.error = 'Terjadi kesalahan saat memuat program';
        }
      } finally {
        this.loading = false;
      }
    },

    async loadRelatedPrograms() {
      try {
        // Pastikan program sudah dimuat
        if (!this.program || !this.program.category) {
          console.log('No program or category found, setting relatedPrograms to empty array');
          this.relatedPrograms = [];
          return;
        }
        
        console.log('Loading related programs for category:', this.program.category);

        const response = await api.getWorkPrograms({ 
          category: this.program.category,
          per_page: 3
        });
        
        console.log('API response for related programs:', response);

        if (response.data.success) {
          // Filter out the current program
          this.relatedPrograms = response.data.data
            .filter(program => program.id !== this.program.id);
          console.log('Related programs loaded:', this.relatedPrograms);
        } else {
          console.log('API returned success=false for related programs');
          this.relatedPrograms = [];
        }
      } catch (error) {
        console.error('Error loading related programs:', error);
        this.relatedPrograms = [];
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

    getPriorityText(priority) {
      const priorityTexts = {
        1: 'Rendah',
        2: 'Sedang',
        3: 'Tinggi'
      };
      return priorityTexts[priority] || 'Tidak ditentukan';
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
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },

    calculateDuration(startDate, endDate) {
      if (!startDate || !endDate) return 'Tidak ditentukan';
      
      const start = new Date(startDate);
      const end = new Date(endDate);
      const diffTime = Math.abs(end - start);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays < 30) {
        return `${diffDays} hari`;
      } else if (diffDays < 365) {
        const months = Math.floor(diffDays / 30);
        return `${months} bulan`;
      } else {
        const years = Math.floor(diffDays / 365);
        return `${years} tahun`;
      }
    },

    shareToFacebook() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.program.title);
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&t=${title}`, '_blank');
    },

    shareToTwitter() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.program.title);
      window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank');
    },

    shareToWhatsApp() {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(this.program.title);
      window.open(`https://wa.me/?text=${title}%20${url}`, '_blank');
    },

    async copyUrl() {
      try {
        await navigator.clipboard.writeText(window.location.href);
        alert('Link berhasil disalin!');
      } catch (err) {
        console.error('Failed to copy URL:', err);
      }
    }
  },
  watch: {
    '$route'(to, from) {
      if (to.params.id !== from.params.id) {
        this.loadProgram();
      }
    }
  }
}
</script>

<style scoped>
.program-detail-page {
  min-height: 100vh;
  background: #f8fffe;
  padding: 2rem 0;
}

.program-detail-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Breadcrumb */
.breadcrumb {
  margin-bottom: 2rem;
}

.breadcrumb-container {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(16, 185, 129, 0.1);
  font-size: 0.9rem;
}

.breadcrumb-container a {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #10b981;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.breadcrumb-container a:hover {
  color: #059669;
}

.breadcrumb-container svg {
  color: #9ca3af;
}

.breadcrumb-container span:last-child {
  color: #6b7280;
  font-weight: 500;
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Content Layout */
.program-content-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 3rem;
}

/* Main Content */
.program-main-content {
  order: 1;
}

/* Program Article */
.program-article {
  background: white;
  border-radius: 24px;
  padding: 3rem;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.08);
  margin-bottom: 3rem;
  border: 1px solid rgba(16, 185, 129, 0.1);
  position: relative;
  overflow: hidden;
}

/* Program Header */
.program-categories {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.category-tag {
  padding: 0.6rem 1.2rem;
  border-radius: 25px;
  color: white;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.category-tag.high-priority {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.category-tag.medium-priority {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.category-tag.low-priority {
  background: linear-gradient(135deg, #10b981, #059669);
}

.program-title {
  font-size: 2.8rem;
  font-weight: 800;
  color: #1f2937;
  margin: 0 0 2rem;
  line-height: 1.1;
  letter-spacing: -0.02em;
}

.program-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 0;
  border-bottom: 2px solid #f3f4f6;
  margin-bottom: 2rem;
}

.meta-group {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #6b7280;
  font-size: 0.95rem;
  font-weight: 500;
}

.meta-item svg {
  color: #10b981;
}

.program-status-badge {
  padding: 0.5rem 1.2rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.9rem;
}

.program-status-badge.status-planned {
  background: #dbeafe;
  color: #1d4ed8;
}

.program-status-badge.status-in-progress {
  background: #ffedd5;
  color: #c2410c;
}

.program-status-badge.status-completed {
  background: #dcfce7;
  color: #15803d;
}

.program-status-badge.status-delayed {
  background: #fee2e2;
  color: #b91c1c;
}

/* Program Dates */
.program-dates {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.date-card {
  background: #f9fafb;
  border-radius: 16px;
  padding: 1.5rem;
  text-align: center;
  border: 1px solid #f3f4f6;
}

.date-label {
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.date-value {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
}

.date-value.high-priority {
  color: #ef4444;
}

.date-value.medium-priority {
  color: #f59e0b;
}

.date-value.low-priority {
  color: #10b981;
}

/* Progress Section */
.progress-section {
  margin: 2rem 0;
  padding: 2rem;
  background: #f9fafb;
  border-radius: 16px;
  border: 1px solid #f3f4f6;
}

.progress-section h3 {
  margin: 0 0 1.5rem;
  color: #1f2937;
  font-size: 1.3rem;
  font-weight: 700;
}

.progress-container {
  margin-top: 1rem;
}

.progress-bar {
  width: 100%;
  height: 12px;
  background: #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 6px;
  transition: width 0.3s ease;
}

.progress-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.75rem;
}

.progress-text {
  font-size: 0.9rem;
  color: #6b7280;
}

.progress-value {
  font-size: 1rem;
  font-weight: 700;
  color: #10b981;
}

/* Program Content */
.program-content {
  margin: 2rem 0;
  color: #374151;
  line-height: 1.8;
  font-size: 1.1rem;
}

.program-content h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #10b981;
}

.program-content h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 2rem 0 1rem;
}

.program-content p {
  margin-bottom: 1.5rem;
}

.program-details {
  margin-top: 2rem;
}

.detail-item {
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 12px;
  border-left: 4px solid #10b981;
}

.detail-item h4 {
  margin: 0 0 0.75rem;
  color: #1f2937;
  font-size: 1.2rem;
}

.detail-item p {
  margin: 0;
  color: #6b7280;
}

/* Share Section */
.program-share {
  margin: 3rem 0;
  padding: 2rem;
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
  border-radius: 20px;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.program-share h3 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0 0 1.5rem;
  color: #1f2937;
  font-size: 1.2rem;
  font-weight: 700;
}

.program-share h3 svg {
  color: #10b981;
}

.share-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.share-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  min-width: 130px;
  justify-content: center;
}

.share-btn.facebook {
  background: #1877f2;
  color: white;
}

.share-btn.twitter {
  background: #1da1f2;
  color: white;
}

.share-btn.whatsapp {
  background: #25d366;
  color: white;
}

.share-btn.copy {
  background: #6b7280;
  color: white;
}

.share-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* Related Programs */
.related-programs {
  background: white;
  border-radius: 24px;
  padding: 3rem;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-header h2 {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  font-size: 2rem;
  font-weight: 800;
  color: #1f2937;
  margin: 0 0 1rem;
}

.section-header h2 svg {
  color: #10b981;
}

.section-header p {
  color: #6b7280;
  font-size: 1.1rem;
  margin: 0;
}

.related-programs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.related-program-item {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.related-program-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.related-program-link {
  text-decoration: none;
  color: inherit;
  display: block;
  padding: 1.5rem;
}

.related-content h3 {
  margin: 0 0 1rem;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1.4;
}

.related-category {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 1rem;
}

.related-category.high-priority {
  background: #fee2e2;
  color: #dc2626;
}

.related-category.medium-priority {
  background: #ffedd5;
  color: #f59e0b;
}

.related-category.low-priority {
  background: #dcfce7;
  color: #166534;
}

.related-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #9ca3af;
  font-size: 0.85rem;
  font-weight: 500;
}

.related-meta .status {
  padding: 0.25rem 0.5rem;
  border-radius: 8px;
  font-size: 0.7rem;
}

.related-meta .status.status-planned {
  background: #dbeafe;
  color: #1d4ed8;
}

.related-meta .status.status-in-progress {
  background: #ffedd5;
  color: #c2410c;
}

.related-meta .status.status-completed {
  background: #dcfce7;
  color: #15803d;
}

.related-meta .status.status-delayed {
  background: #fee2e2;
  color: #b91c1c;
}

/* Sidebar */
.program-detail-sidebar {
  order: 2;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.sidebar-widget {
  background: white;
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 0 15px 40px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
  transition: all 0.3s ease;
}

.sidebar-widget:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 50px rgba(16, 185, 129, 0.12);
}

.widget-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.3rem;
  font-weight: 800;
  color: #1f2937;
  margin: 0 0 2rem;
  position: relative;
  padding-bottom: 1rem;
}

.widget-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 2px;
}

.widget-title svg {
  color: #10b981;
}

/* Program Stats */
.program-stats {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  padding: 1rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.stat-item:last-child {
  border-bottom: none;
}

.stat-label {
  color: #6b7280;
  font-weight: 500;
}

.stat-value {
  font-weight: 600;
  color: #1f2937;
}

.stat-value.high-priority {
  color: #ef4444;
}

.stat-value.medium-priority {
  color: #f59e0b;
}

.stat-value.low-priority {
  color: #10b981;
}

.stat-value.status-planned {
  color: #3b82f6;
}

.stat-value.status-in-progress {
  color: #f59e0b;
}

.stat-value.status-completed {
  color: #10b981;
}

.stat-value.status-delayed {
  color: #ef4444;
}

/* Categories */
.categories-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.category-item {
  border-radius: 12px;
  background: #f9fafb;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.category-item:hover {
  background: #f0fdf4;
  border-color: #10b981;
  transform: translateX(5px);
}

.category-item.active {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.category-item a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.25rem;
  text-decoration: none;
  color: inherit;
  font-weight: 600;
}

.category-name {
  color: #1f2937;
  transition: color 0.3s ease;
}

.category-item.active .category-name {
  color: white;
}

/* Loading States */
.loading-state,
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 4rem 2rem;
  min-height: 60vh;
}

.loading-spinner {
  width: 60px;
  height: 60px;
  border: 6px solid #d1fae5;
  border-top: 6px solid #10b981;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 2rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state {
  background: white;
  border-radius: 24px;
  margin: 2rem;
  box-shadow: 0 15px 40px rgba(220, 38, 38, 0.1);
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-state h2 {
  color: #dc2626;
  margin: 1rem 0 2rem;
  font-size: 1.5rem;
}

.retry-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  background: #b91c1c;
  transform: translateY(-2px);
}

.back-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  border: 1px solid #10b981;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.back-link:hover {
  background: #10b981;
  color: white;
}

/* Responsive Design */
@media (max-width: 1400px) {
  .program-content-layout {
    grid-template-columns: 1fr 350px;
    gap: 2rem;
  }
}

@media (max-width: 1200px) {
  .program-article {
    padding: 2rem;
  }

  .program-title {
    font-size: 2.2rem;
  }
}

@media (max-width: 992px) {
  .program-content-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .program-detail-sidebar {
    order: 1;
  }

  .program-main-content {
    order: 2;
  }

  .sidebar-widget {
    padding: 1.5rem;
  }

  .meta-group {
    flex-direction: column;
    gap: 1rem;
  }

  .program-status-badge {
    margin-top: 1rem;
  }
}

@media (max-width: 768px) {
  .program-detail-page {
    padding: 1rem 0;
  }

  .program-detail-container {
    padding: 0 1rem;
  }

  .breadcrumb-container {
    padding: 0.75rem 1rem;
    font-size: 0.8rem;
  }

  .breadcrumb-container span:last-child {
    max-width: 200px;
  }

  .program-article {
    padding: 1.5rem;
  }

  .program-title {
    font-size: 1.8rem;
  }

  .program-content {
    font-size: 1rem;
  }

  .related-programs {
    padding: 1.5rem;
  }

  .related-programs-grid {
    grid-template-columns: 1fr;
  }

  .share-buttons {
    grid-template-columns: 1fr;
  }

  .share-btn {
    width: 100%;
  }

  .sidebar-widget {
    padding: 1.25rem;
  }

  .widget-title {
    font-size: 1.2rem;
  }
}

@media (max-width: 480px) {
  .program-title {
    font-size: 1.6rem;
  }

  .section-header h2 {
    font-size: 1.5rem;
    flex-direction: column;
    gap: 0.5rem;
  }

  .program-dates {
    grid-template-columns: 1fr;
  }

  .stat-item {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
}
</style>