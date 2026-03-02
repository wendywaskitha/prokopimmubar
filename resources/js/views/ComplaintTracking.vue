<template>
  <div class="complaint-tracking-page">
    <!-- Hero Header -->
    <div class="tracking-hero">
      <div class="tracking-hero-content">
        <h1>Lacak Status Aduan</h1>
        <p>Periksa status penanganan aduan Anda dengan mudah</p>
      </div>
    </div>

    <div class="tracking-container">
      <!-- Tracking Form -->
      <div class="tracking-card">
        <div class="card-header">
          <h2>Cari Aduan Anda</h2>
          <p>Masukkan informasi untuk melacak status aduan</p>
        </div>

        <form @submit.prevent="trackComplaint" class="tracking-form">
          <div class="form-group">
            <label for="trackingMethod">Metode Pelacakan *</label>
            <select 
              id="trackingMethod" 
              v-model="trackingMethod" 
              class="form-control"
              @change="clearResults"
            >
              <option value="">Pilih Metode Pelacakan</option>
              <option value="id">Melalui ID Aduan</option>
              <option value="email">Melalui Email</option>
            </select>
          </div>

          <div v-if="trackingMethod === 'id'" class="form-group">
            <label for="complaintId">ID Aduan *</label>
            <input 
              type="text" 
              id="complaintId" 
              v-model="complaintId" 
              class="form-control"
              placeholder="Masukkan ID aduan yang diberikan saat pengiriman"
              required
            >
          </div>

          <div v-if="trackingMethod === 'email'" class="form-group">
            <label for="email">Email *</label>
            <input 
              type="email" 
              id="email" 
              v-model="email" 
              class="form-control"
              placeholder="Masukkan email yang digunakan saat mengirim aduan"
              required
            >
          </div>

          <div class="form-actions">
            <button type="submit" class="track-btn" :disabled="isSearching">
              <span v-if="isSearching">
                <div class="loading-spinner"></div>
                Mencari...
              </span>
              <span v-else>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"/>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Lacak Aduan
              </span>
            </button>
          </div>
        </form>

        <!-- Results -->
        <div v-if="complaints.length > 0" class="results-section">
          <h3>Hasil Pencarian</h3>
          <div class="complaints-list">
            <div 
              v-for="complaint in complaints" 
              :key="complaint.id" 
              class="complaint-item"
              @click="selectComplaint(complaint)"
            >
              <div class="complaint-header">
                <div class="complaint-id">#{{ complaint.id }}</div>
                <div class="complaint-status" :class="complaint.status">
                  {{ getStatusText(complaint.status) }}
                </div>
              </div>
              <div class="complaint-title">{{ complaint.category }}</div>
              <div class="complaint-description">{{ truncateDescription(complaint.description) }}</div>
              <div class="complaint-date">{{ formatDate(complaint.created_at) }}</div>
            </div>
          </div>
        </div>

        <div v-if="noResults" class="no-results">
          <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
          <h3>Tidak Ada Aduan Ditemukan</h3>
          <p>Silakan periksa kembali informasi yang Anda masukkan atau hubungi kami jika ada kendala.</p>
        </div>
      </div>

      <!-- Selected Complaint Detail -->
      <div v-if="selectedComplaint" class="detail-card">
        <div class="detail-header">
          <h2>Detail Aduan</h2>
          <button @click="selectedComplaint = null" class="close-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <div class="complaint-detail">
          <div class="detail-row">
            <span class="label">ID Aduan:</span>
            <span class="value">#{{ selectedComplaint.id }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Tanggal Pengaduan:</span>
            <span class="value">{{ formatDate(selectedComplaint.created_at) }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Status:</span>
            <span class="value">
              <span class="status-badge" :class="selectedComplaint.status">
                {{ getStatusText(selectedComplaint.status) }}
              </span>
            </span>
          </div>

          <div class="detail-row">
            <span class="label">Kategori:</span>
            <span class="value">{{ selectedComplaint.category }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Nama Pelapor:</span>
            <span class="value">{{ selectedComplaint.name }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Email:</span>
            <span class="value">{{ selectedComplaint.email }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Telepon:</span>
            <span class="value">{{ selectedComplaint.phone }}</span>
          </div>

          <div class="detail-row">
            <span class="label">Deskripsi:</span>
            <span class="value description">{{ selectedComplaint.description }}</span>
          </div>

          <div v-if="selectedComplaint.response" class="detail-row">
            <span class="label">Respon Petugas:</span>
            <span class="value response">{{ selectedComplaint.response }}</span>
          </div>
        </div>

        <!-- Status Timeline -->
        <div class="timeline-section">
          <h3>Timeline Status</h3>
          <div class="status-timeline">
            <div class="timeline-item" :class="{ active: selectedComplaint.status === 'baru' }">
              <div class="timeline-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12,6 12,12 16,14"/>
                </svg>
              </div>
              <div class="timeline-content">
                <h4>Aduan Diterima</h4>
                <p>Aduan Anda telah diterima sistem</p>
              </div>
            </div>

            <div class="timeline-item" :class="{ active: selectedComplaint.status === 'diproses' }">
              <div class="timeline-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
              </div>
              <div class="timeline-content">
                <h4>Sedang Diproses</h4>
                <p>Aduan sedang dalam proses penanganan</p>
              </div>
            </div>

            <div class="timeline-item" :class="{ active: selectedComplaint.status === 'selesai' }">
              <div class="timeline-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="20,6 9,17 4,12"/>
                </svg>
              </div>
              <div class="timeline-content">
                <h4>Selesai</h4>
                <p>Aduan telah selesai ditangani</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api.js';

export default {
  name: 'ComplaintTracking',
  data() {
    return {
      trackingMethod: '',
      complaintId: '',
      email: '',
      complaints: [],
      selectedComplaint: null,
      isSearching: false,
      noResults: false
    }
  },
  methods: {
    async trackComplaint() {
      if (!this.trackingMethod) {
        alert('Silakan pilih metode pelacakan terlebih dahulu.');
        return;
      }

      if ((this.trackingMethod === 'id' && !this.complaintId) || 
          (this.trackingMethod === 'email' && !this.email)) {
        alert('Silakan lengkapi informasi yang diperlukan.');
        return;
      }

      this.isSearching = true;
      this.noResults = false;
      this.complaints = [];
      this.selectedComplaint = null;

      try {
        let response;
        
        if (this.trackingMethod === 'id') {
          // Get specific complaint by ID
          response = await api.getComplaintById(this.complaintId);
          if (response.data.success) {
            this.complaints = [response.data.data];
          }
        } else if (this.trackingMethod === 'email') {
          // Get complaints by email
          response = await api.getComplaints({ email: this.email });
          if (response.data.success) {
            this.complaints = response.data.data;
          }
        }

        if (this.complaints.length === 0) {
          this.noResults = true;
        }
      } catch (error) {
        console.error('Error tracking complaint:', error);
        alert('Terjadi kesalahan saat mencari aduan. Silakan coba lagi.');
        this.noResults = true;
      } finally {
        this.isSearching = false;
      }
    },

    selectComplaint(complaint) {
      this.selectedComplaint = complaint;
    },

    clearResults() {
      this.complaints = [];
      this.selectedComplaint = null;
      this.noResults = false;
      this.complaintId = '';
      this.email = '';
    },

    getStatusText(status) {
      const statusMap = {
        'baru': 'Baru',
        'diproses': 'Diproses',
        'selesai': 'Selesai',
        'ditolak': 'Ditolak'
      };
      return statusMap[status] || status;
    },

    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString('id-ID', options);
    },

    truncateDescription(description) {
      if (description.length > 100) {
        return description.substring(0, 100) + '...';
      }
      return description;
    }
  }
}
</script>

<style scoped>
.complaint-tracking-page {
  min-height: 100vh;
  background: #f8fffe;
}

/* Hero Section */
.tracking-hero {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 4rem 0 3rem;
  text-align: center;
}

.tracking-hero-content h1 {
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.tracking-hero-content p {
  font-size: 1.2rem;
  opacity: 0.95;
  margin: 0;
}

/* Container */
.tracking-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-top: 2rem;
  margin-bottom: 3rem;
}

/* Tracking Card */
.tracking-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.card-header {
  text-align: center;
  margin-bottom: 2rem;
}

.card-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem;
}

.card-header p {
  color: #6b7280;
  margin: 0;
}

/* Form */
.tracking-form {
  margin-bottom: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.75rem;
  font-weight: 600;
  color: #1f2937;
  font-size: 0.95rem;
}

.form-control {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background: white;
}

.form-control:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-actions {
  text-align: center;
  margin-top: 2rem;
}

.track-btn {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 1rem 3rem;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.track-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.track-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Results */
.results-section h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1.5rem;
}

.complaints-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.complaint-item {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #fafbfc;
}

.complaint-item:hover {
  background: #f0fdf4;
  border-color: #10b981;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
}

.complaint-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.complaint-id {
  font-weight: 700;
  color: #10b981;
  font-size: 1.1rem;
}

.complaint-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.complaint-status.baru {
  background: #dcfce7;
  color: #10b981;
}

.complaint-status.diproses {
  background: #ffedd5;
  color: #ea580c;
}

.complaint-status.selesai {
  background: #dcfce7;
  color: #16a34a;
}

.complaint-status.ditolak {
  background: #fee2e2;
  color: #dc2626;
}

.complaint-title {
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.complaint-description {
  color: #6b7280;
  margin-bottom: 0.75rem;
  font-size: 0.9rem;
  line-height: 1.5;
}

.complaint-date {
  color: #9ca3af;
  font-size: 0.85rem;
}

.no-results {
  text-align: center;
  padding: 3rem 2rem;
  color: #9ca3af;
}

.no-results svg {
  margin-bottom: 1.5rem;
  opacity: 0.5;
}

.no-results h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #6b7280;
  margin: 0 0 1rem;
}

.no-results p {
  margin: 0;
  font-size: 1rem;
  line-height: 1.5;
}

/* Detail Card */
.detail-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.1);
  position: sticky;
  top: 2rem;
  max-height: calc(100vh - 4rem);
  overflow-y: auto;
}

.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.detail-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.close-btn {
  background: #f3f4f6;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: #e5e7eb;
}

.complaint-detail {
  margin-bottom: 2rem;
}

.detail-row {
  display: flex;
  margin-bottom: 1.5rem;
  align-items: flex-start;
}

.label {
  font-weight: 600;
  color: #6b7280;
  width: 30%;
  min-width: 120px;
}

.value {
  flex: 1;
  color: #1f2937;
  font-weight: 500;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.baru {
  background: #dcfce7;
  color: #10b981;
}

.status-badge.diproses {
  background: #ffedd5;
  color: #ea580c;
}

.status-badge.selesai {
  background: #dcfce7;
  color: #16a34a;
}

.status-badge.ditolak {
  background: #fee2e2;
  color: #dc2626;
}

.description, .response {
  white-space: pre-wrap;
  line-height: 1.6;
}

.response {
  background: #f0fdf4;
  padding: 1rem;
  border-radius: 8px;
  border-left: 4px solid #10b981;
}

/* Timeline */
.timeline-section h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1.5rem;
}

.status-timeline {
  position: relative;
}

.status-timeline::before {
  content: '';
  position: absolute;
  left: 20px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: linear-gradient(135deg, #10b981, #059669);
}

.timeline-item {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  position: relative;
  padding-left: 3rem;
}

.timeline-item.active .timeline-icon {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.timeline-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: white;
  border: 2px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  position: absolute;
  left: 0;
  z-index: 1;
}

.timeline-content h4 {
  margin: 0 0 0.5rem;
  color: #1f2937;
  font-weight: 600;
}

.timeline-content p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .tracking-container {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .detail-card {
    position: static;
    max-height: none;
  }
}

@media (max-width: 768px) {
  .tracking-hero-content h1 {
    font-size: 2.5rem;
  }
  
  .tracking-container {
    padding: 0 1rem;
    margin-top: 1rem;
  }
  
  .tracking-card, .detail-card {
    padding: 1.5rem;
  }
  
  .card-header h2, .detail-header h2 {
    font-size: 1.5rem;
  }
  
  .detail-row {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .label {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .tracking-hero-content h1 {
    font-size: 2rem;
  }
  
  .tracking-hero-content p {
    font-size: 1rem;
  }
  
  .tracking-container {
    padding: 0 0.5rem;
  }
  
  .tracking-card, .detail-card {
    padding: 1rem;
    border-radius: 15px;
  }
  
  .timeline-item {
    padding-left: 2.5rem;
  }
  
  .timeline-icon {
    width: 30px;
    height: 30px;
  }
}
</style>