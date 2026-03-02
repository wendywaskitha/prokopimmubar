<template>
  <div class="programs-page">
    <!-- Hero Header -->
    <div class="programs-hero">
      <div class="programs-hero-content">
        <h1>Program Kerja</h1>
        <p>Program-program kerja kepala daerah Kabupaten Muna Barat</p>
      </div>
    </div>

    <div class="programs-container">
      <!-- Filters Section -->
      <div class="programs-filters">
        <div class="filter-group">
          <div class="filter-item">
            <label>Kategori</label>
            <select v-model="selectedCategory" class="filter-select">
              <option value="">Semua Kategori</option>
              <option value="infrastruktur">Infrastruktur</option>
              <option value="pendidikan">Pendidikan</option>
              <option value="kesehatan">Kesehatan</option>
              <option value="pariwisata">Pariwisata</option>
              <option value="pertanian">Pertanian</option>
              <option value="lingkungan">Lingkungan</option>
              <option value="sosial">Sosial</option>
              <option value="ekonomi">Ekonomi</option>
            </select>
          </div>

          <div class="filter-item">
            <label>Status</label>
            <select v-model="selectedStatus" class="filter-select">
              <option value="">Semua Status</option>
              <option value="planned">Direncanakan</option>
              <option value="in_progress">Dalam Proses</option>
              <option value="completed">Selesai</option>
              <option value="delayed">Terlambat</option>
            </select>
          </div>

          <div class="filter-item">
            <label>Prioritas</label>
            <select v-model="selectedPriority" class="filter-select">
              <option value="">Semua Prioritas</option>
              <option value="1">Rendah</option>
              <option value="2">Sedang</option>
              <option value="3">Tinggi</option>
            </select>
          </div>

          <div class="filter-item">
            <label>Cari Program</label>
            <div class="search-input-group">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Masukkan kata kunci..."
                class="search-input"
                @keyup.enter="handleSearch"
              >
              <button class="search-btn" @click="handleSearch">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="filter-actions">
          <button class="filter-reset" @click="resetFilters">Reset Filter</button>
          <div class="view-options">
            <select class="sort-select" v-model="sortBy">
              <option value="latest">Terbaru</option>
              <option value="oldest">Terlama</option>
              <option value="priority">Prioritas</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="programs-content">
        <!-- Main Programs -->
        <div class="programs-main">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Memuat program kerja...</p>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <div class="error-icon">⚠️</div>
            <p>{{ error }}</p>
            <button @click="loadPrograms" class="retry-btn">Coba Lagi</button>
          </div>

          <!-- Programs Grid/List -->
          <div v-else>
            <!-- Results Info -->
            <div class="results-info" v-if="programs.length > 0">
              <p>Menampilkan {{ programs.length }} program kerja</p>
              <div class="sort-options">
                <select class="sort-select" v-model="sortBy">
                  <option value="latest">Terbaru</option>
                  <option value="oldest">Terlama</option>
                  <option value="priority">Prioritas</option>
                </select>
              </div>
            </div>

            <!-- Programs Grid -->
            <div class="programs-grid">
              <div 
                class="program-card" 
                v-for="program in programs" 
                :key="program.id"
                :class="getPriorityClass(program.priority)"
                @click="viewProgram(program.id)"
              >
                <div class="card-header">
                  <div class="header-content">
                    <span class="category-tag">{{ program.category }}</span>
                    <span class="program-status" :class="getStatusClass(program.status)">
                      {{ getStatusText(program.status) }}
                    </span>
                  </div>
                  <div class="priority-indicator" :class="getPriorityClass(program.priority)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polygon points="12,2 15.09,8.26 22,9 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9 8.91,8.26"/>
                    </svg>
                  </div>
                </div>
                <div class="card-content">
                  <h3 class="card-title">{{ program.title }}</h3>
                  <p class="card-description">{{ program.description }}</p>
                  <div class="card-meta">
                    <div class="meta-item">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                      </svg>
                      <span>{{ formatDate(program.start_date) }} - {{ formatDate(program.end_date) }}</span>
                    </div>
                    <div class="meta-item">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                      </svg>
                      <span>{{ program.author?.name || 'Admin' }}</span>
                    </div>
                  </div>
                  <div class="progress-container" v-if="program.progress !== undefined">
                    <div class="progress-bar">
                      <div class="progress-fill" :style="{ width: program.progress + '%' }"></div>
                    </div>
                    <div class="progress-info">
                      <span class="progress-text">{{ program.progress }}% Complete</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="programs.length === 0" class="empty-state">
              <div class="empty-icon">📋</div>
              <h3>Tidak Ada Program Ditemukan</h3>
              <p>Coba ubah filter atau kata kunci pencarian Anda</p>
              <button @click="resetFilters" class="reset-filters-btn">Reset Filter</button>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper" v-if="totalPages > 1">
              <nav class="pagination">
                <button class="page-btn prev" @click="prevPage" :disabled="currentPage === 1">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 18l-6-6 6-6"/>
                  </svg>
                  Sebelumnya
                </button>

                <div class="page-numbers">
                  <button
                    class="page-number"
                    :class="{ active: currentPage === page }"
                    v-for="page in visiblePages"
                    :key="page"
                    @click="changePage(page)"
                    v-if="page !== '...'"
                  >
                    {{ page }}
                  </button>
                  <span v-else class="page-dots">...</span>
                </div>

                <button class="page-btn next" @click="nextPage" :disabled="currentPage === totalPages">
                  Selanjutnya
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"/>
                  </svg>
                </button>
              </nav>

              <div class="pagination-info">
                <p>Halaman {{ currentPage }} dari {{ totalPages }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <aside class="programs-sidebar">
          <!-- Stats Widget -->
          <div class="sidebar-widget stats-widget">
            <h3 class="widget-title">Statistik Program</h3>
            <div class="stats-grid">
              <div class="stat-item">
                <div class="stat-number">{{ totalPrograms }}</div>
                <div class="stat-label">Total Program</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">{{ inProgressCount }}</div>
                <div class="stat-label">Dalam Proses</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">{{ completedCount }}</div>
                <div class="stat-label">Selesai</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">{{ highPriorityCount }}</div>
                <div class="stat-label">Prioritas Tinggi</div>
              </div>
            </div>
          </div>

          <!-- Categories Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Kategori Program</h3>
            <div class="category-list">
              <div 
                class="category-item" 
                v-for="category in categories" 
                :key="category.value"
                @click="filterByCategory(category.value)"
              >
                <span class="category-name">{{ category.name }}</span>
                <span class="category-count">{{ category.count }}</span>
              </div>
            </div>
          </div>

          <!-- Priority Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Prioritas Program</h3>
            <div class="priority-list">
              <div class="priority-item" @click="filterByPriority(3)">
                <div class="priority-indicator high-priority">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12,2 15.09,8.26 22,9 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9 8.91,8.26"/>
                  </svg>
                </div>
                <span class="priority-name">Tinggi</span>
                <span class="priority-count">{{ priorityCounts[3] || 0 }}</span>
              </div>
              <div class="priority-item" @click="filterByPriority(2)">
                <div class="priority-indicator medium-priority">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12,2 15.09,8.26 22,9 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9 8.91,8.26"/>
                  </svg>
                </div>
                <span class="priority-name">Sedang</span>
                <span class="priority-count">{{ priorityCounts[2] || 0 }}</span>
              </div>
              <div class="priority-item" @click="filterByPriority(1)">
                <div class="priority-indicator low-priority">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12,2 15.09,8.26 22,9 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9 8.91,8.26"/>
                  </svg>
                </div>
                <span class="priority-name">Rendah</span>
                <span class="priority-count">{{ priorityCounts[1] || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Status Widget -->
          <div class="sidebar-widget">
            <h3 class="widget-title">Status Program</h3>
            <div class="status-list">
              <div class="status-item" @click="filterByStatus('planned')">
                <span class="status-indicator status-planned"></span>
                <span class="status-name">Direncanakan</span>
                <span class="status-count">{{ statusCounts.planned || 0 }}</span>
              </div>
              <div class="status-item" @click="filterByStatus('in_progress')">
                <span class="status-indicator status-in-progress"></span>
                <span class="status-name">Dalam Proses</span>
                <span class="status-count">{{ statusCounts.in_progress || 0 }}</span>
              </div>
              <div class="status-item" @click="filterByStatus('completed')">
                <span class="status-indicator status-completed"></span>
                <span class="status-name">Selesai</span>
                <span class="status-count">{{ statusCounts.completed || 0 }}</span>
              </div>
              <div class="status-item" @click="filterByStatus('delayed')">
                <span class="status-indicator status-delayed"></span>
                <span class="status-name">Terlambat</span>
                <span class="status-count">{{ statusCounts.delayed || 0 }}</span>
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
  name: 'Programs',
  data() {
    return {
      programs: [],
      selectedCategory: '',
      selectedStatus: '',
      selectedPriority: '',
      searchQuery: '',
      sortBy: 'latest',
      currentPage: 1,
      totalPages: 1,
      totalPrograms: 0,
      inProgressCount: 0,
      completedCount: 0,
      highPriorityCount: 0,
      priorityCounts: {},
      statusCounts: {},
      loading: false,
      error: null,
      categories: [
        { name: 'Infrastruktur', value: 'infrastruktur', count: 0 },
        { name: 'Pendidikan', value: 'pendidikan', count: 0 },
        { name: 'Kesehatan', value: 'kesehatan', count: 0 },
        { name: 'Pariwisata', value: 'pariwisata', count: 0 },
        { name: 'Pertanian', value: 'pertanian', count: 0 },
        { name: 'Lingkungan', value: 'lingkungan', count: 0 },
        { name: 'Sosial', value: 'sosial', count: 0 },
        { name: 'Ekonomi', value: 'ekonomi', count: 0 }
      ]
    }
  },
  computed: {
    visiblePages() {
      const pages = [];
      const current = this.currentPage;
      const total = this.totalPages;

      if (total <= 7) {
        for (let i = 1; i <= total; i++) {
          pages.push(i);
        }
      } else {
        if (current <= 4) {
          for (let i = 1; i <= 5; i++) pages.push(i);
          pages.push('...');
          pages.push(total);
        } else if (current >= total - 3) {
          pages.push(1);
          pages.push('...');
          for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
          pages.push(1);
          pages.push('...');
          for (let i = current - 1; i <= current + 1; i++) pages.push(i);
          pages.push('...');
          pages.push(total);
        }
      }

      return pages;
    }
  },
  async mounted() {
    await this.loadPrograms();
    await this.loadStats();
  },
  watch: {
    selectedCategory() {
      this.currentPage = 1;
      this.loadPrograms();
    },
    selectedStatus() {
      this.currentPage = 1;
      this.loadPrograms();
    },
    selectedPriority() {
      this.currentPage = 1;
      this.loadPrograms();
    },
    searchQuery() {
      this.currentPage = 1;
      this.loadPrograms();
    },
    sortBy() {
      this.currentPage = 1;
      this.loadPrograms();
    }
  },
  methods: {
    async loadPrograms() {
      this.loading = true;
      this.error = null;

      try {
        const params = {
          page: this.currentPage,
          per_page: 9
        };

        if (this.selectedCategory) {
          params.category = this.selectedCategory;
        }

        if (this.selectedStatus) {
          params.status = this.selectedStatus;
        }

        if (this.selectedPriority) {
          params.priority = this.selectedPriority;
        }

        if (this.searchQuery) {
          params.search = this.searchQuery;
        }

        if (this.sortBy) {
          params.sort = this.sortBy;
        }

        const response = await api.getWorkPrograms(params);
        
        if (response.data.success) {
          this.programs = response.data.data;
          this.totalPages = response.data.pagination.last_page;
          this.totalPrograms = response.data.pagination.total;
        } else {
          this.error = 'Gagal memuat program kerja. Silakan coba lagi.';
        }
      } catch (error) {
        this.error = 'Terjadi kesalahan saat memuat program kerja.';
        console.error('Error loading programs:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async loadStats() {
      try {
        // Load all programs to calculate stats
        const response = await api.getWorkPrograms({ per_page: 100 });
        
        if (response.data.success) {
          const allPrograms = response.data.data;
          
          // Calculate status counts
          this.statusCounts = {};
          this.priorityCounts = {};
          
          allPrograms.forEach(program => {
            // Status counts
            if (this.statusCounts[program.status]) {
              this.statusCounts[program.status]++;
            } else {
              this.statusCounts[program.status] = 1;
            }
            
            // Priority counts
            if (this.priorityCounts[program.priority]) {
              this.priorityCounts[program.priority]++;
            } else {
              this.priorityCounts[program.priority] = 1;
            }
            
            // Category counts
            const category = this.categories.find(c => c.value === program.category);
            if (category) {
              category.count++;
            }
          });
          
          // Calculate specific counts
          this.inProgressCount = this.statusCounts.in_progress || 0;
          this.completedCount = this.statusCounts.completed || 0;
          this.highPriorityCount = this.priorityCounts[3] || 0;
        }
      } catch (error) {
        console.error('Error loading stats:', error);
      }
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.currentPage = page;
        this.loadPrograms();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.changePage(this.currentPage + 1);
      }
    },
    
    prevPage() {
      if (this.currentPage > 1) {
        this.changePage(this.currentPage - 1);
      }
    },
    
    resetFilters() {
      this.selectedCategory = '';
      this.selectedStatus = '';
      this.selectedPriority = '';
      this.searchQuery = '';
      this.sortBy = 'latest';
      this.currentPage = 1;
      this.loadPrograms();
    },
    
    handleSearch() {
      this.currentPage = 1;
      this.loadPrograms();
    },
    
    filterByCategory(category) {
      this.selectedCategory = category;
      this.currentPage = 1;
      this.loadPrograms();
    },
    
    filterByStatus(status) {
      this.selectedStatus = status;
      this.currentPage = 1;
      this.loadPrograms();
    },
    
    filterByPriority(priority) {
      this.selectedPriority = priority.toString();
      this.currentPage = 1;
      this.loadPrograms();
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
        month: 'short',
        day: 'numeric'
      };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    
    viewProgram(id) {
      this.$router.push(`/programs/${id}`);
    }
  }
}
</script>

<style scoped>
.programs-page {
  min-height: 100vh;
  background: #f8fffe;
}

/* Hero Section */
.programs-hero {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 4rem 0 3rem;
  text-align: center;
}

.programs-hero-content h1 {
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.programs-hero-content p {
  font-size: 1.2rem;
  opacity: 0.95;
  margin: 0;
}

/* Container */
.programs-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Filters */
.programs-filters {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  padding: 2rem;
  margin: -2rem 0 3rem;
  position: relative;
  z-index: 10;
}

.filter-group {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.filter-item {
  min-width: 200px;
}

.filter-item label {
  display: block;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.filter-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  background: white;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-input-group {
  display: flex;
  position: relative;
}

.search-input {
  flex: 1;
  padding: 0.75rem 3rem 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-btn {
  position: absolute;
  right: 0.5rem;
  top: 50%;
  transform: translateY(-50%);
  background: #10b981;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-btn:hover {
  background: #059669;
}

.filter-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filter-reset {
  background: #f3f4f6;
  color: #4b5563;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-reset:hover {
  background: #e5e7eb;
}

.sort-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  background: white;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.sort-select:focus {
  outline: none;
  border-color: #10b981;
}

/* Content Layout */
.programs-content {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 3rem;
  align-items: start;
}

/* Results Info */
.results-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.results-info p {
  margin: 0;
  color: #6b7280;
  font-weight: 500;
}

/* Programs Grid */
.programs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.program-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  transition: all 0.3s ease;
  border: 1px solid rgba(16, 185, 129, 0.1);
  cursor: pointer;
}

.program-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.5rem 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.header-content {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.category-tag {
  padding: 0.3rem 0.8rem;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: #f3f4f6;
  color: #4b5563;
}

.program-status {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.3rem 0.8rem;
  border-radius: 15px;
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

.priority-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.priority-indicator.high-priority {
  background: #fee2e2;
  color: #dc2626;
}

.priority-indicator.medium-priority {
  background: #ffedd5;
  color: #f59e0b;
}

.priority-indicator.low-priority {
  background: #dcfce7;
  color: #166534;
}

.card-content {
  padding: 1.5rem;
}

.card-title {
  margin: 0 0 1rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1.4;
}

.card-description {
  color: #6b7280;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-meta {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #9ca3af;
  font-size: 0.85rem;
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

.progress-info {
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
}

.progress-text {
  font-size: 0.8rem;
  color: #6b7280;
}

/* States */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 5px solid #d1fae5;
  border-top: 5px solid #10b981;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1.5rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #dc2626;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.retry-btn {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 1rem;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.reset-filters-btn {
  background: #10b981;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 1rem;
}

/* Pagination */
.pagination-wrapper {
  margin-top: 3rem;
  text-align: center;
}

.pagination {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  padding: 1rem;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.page-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border: none;
  background: transparent;
  color: #6b7280;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.page-btn:hover:not(:disabled) {
  background: #f3f4f6;
  color: #10b981;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 0.25rem;
}

.page-number {
  width: 40px;
  height: 40px;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #6b7280;
  font-weight: 500;
}

.page-number:hover {
  background: #f3f4f6;
  color: #10b981;
}

.page-number.active {
  background: #10b981;
  color: white;
}

.page-dots {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
}

.pagination-info {
  margin-top: 1rem;
  color: #6b7280;
  font-size: 0.9rem;
}

/* Sidebar */
.programs-sidebar {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.sidebar-widget {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.widget-title {
  margin: 0 0 1.5rem;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  position: relative;
  padding-bottom: 0.75rem;
}

.widget-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 3px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 2px;
}

/* Stats Widget */
.stats-widget {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.stats-widget .widget-title::after {
  background: rgba(255,255,255,0.3);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.stats-grid .stat-item {
  text-align: center;
  padding: 1rem;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.stat-number {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.85rem;
  opacity: 0.9;
}

/* Category List */
.category-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  background: #f9fafb;
  border-radius: 10px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.category-item:hover {
  background: #f3f4f6;
  transform: translateX(3px);
}

.category-name {
  color: #1f2937;
  font-weight: 500;
}

.category-count {
  background: #10b981;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

/* Priority List */
.priority-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.priority-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background: #f9fafb;
  border-radius: 10px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.priority-item:hover {
  background: #f3f4f6;
  transform: translateX(3px);
}

.priority-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
}

.priority-indicator.high-priority {
  background: #fee2e2;
  color: #dc2626;
}

.priority-indicator.medium-priority {
  background: #ffedd5;
  color: #f59e0b;
}

.priority-indicator.low-priority {
  background: #dcfce7;
  color: #166534;
}

.priority-name {
  flex: 1;
  color: #1f2937;
  font-weight: 500;
}

.priority-count {
  background: #10b981;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

/* Status List */
.status-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background: #f9fafb;
  border-radius: 10px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.status-item:hover {
  background: #f3f4f6;
  transform: translateX(3px);
}

.status-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.status-indicator.status-planned {
  background: #3b82f6;
}

.status-indicator.status-in-progress {
  background: #f59e0b;
}

.status-indicator.status-completed {
  background: #10b981;
}

.status-indicator.status-delayed {
  background: #ef4444;
}

.status-name {
  flex: 1;
  color: #1f2937;
  font-weight: 500;
}

.status-count {
  background: #10b981;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .programs-content {
    grid-template-columns: 1fr 300px;
    gap: 2rem;
  }
  
  .programs-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
}

@media (max-width: 992px) {
  .programs-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .programs-sidebar {
    order: -1;
  }

  .sidebar-widget {
    padding: 1.5rem;
  }

  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .programs-hero-content h1 {
    font-size: 2.5rem;
  }

  .programs-container {
    padding: 0 1rem;
  }

  .programs-filters {
    padding: 1.5rem;
    margin: -1.5rem 0 2rem;
  }

  .filter-group {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .filter-item {
    min-width: auto;
  }

  .filter-actions {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .programs-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .pagination {
    flex-wrap: wrap;
    gap: 0.25rem;
  }

  .page-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .programs-hero-content h1 {
    font-size: 2rem;
  }

  .programs-grid {
    grid-template-columns: 1fr;
  }

  .card-content {
    padding: 1rem;
  }

  .card-title {
    font-size: 1.1rem;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>