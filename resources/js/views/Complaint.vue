<template>
  <div class="complaint-page">
    <!-- Hero Header -->
    <div class="complaint-hero">
      <div class="complaint-hero-content">
        <h1>Aduan Masyarakat</h1>
        <p>Sampaikan aspirasi dan keluhan Anda untuk kemajuan Kabupaten Muna Barat</p>
      </div>
    </div>

    <div class="complaint-container">
      <!-- Stats Banner -->
      <div class="stats-banner">
        <div class="stat-item">
          <div class="stat-number">{{ (stats.total || 0).toLocaleString() }}</div>
          <div class="stat-label">Total Aduan</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ (stats.completed || 0).toLocaleString() }}</div>
          <div class="stat-label">Sudah Ditangani</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ (stats.inProgress || 0).toLocaleString() }}</div>
          <div class="stat-label">Dalam Proses</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ (stats.pending || 0).toLocaleString() }}</div>
          <div class="stat-label">Belum Ditangani</div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="complaint-content">
        <!-- Form Section -->
        <div class="form-section">
          <div class="section-header">
            <h2>Formulir Aduan</h2>
            <p>Isi formulir di bawah ini dengan lengkap dan jelas</p>
          </div>

          <!-- Progress Steps -->
          <div class="progress-steps">
            <div class="step active">
              <div class="step-number">1</div>
              <div class="step-label">Data Diri</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
              <div class="step-number">2</div>
              <div class="step-label">Detail Aduan</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
              <div class="step-number">3</div>
              <div class="step-label">Lampiran</div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitComplaint" class="complaint-form">
            <!-- Personal Information -->
            <div class="form-section-group">
              <h3 class="group-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
                Data Diri
              </h3>

              <div class="form-row">
                <div class="form-group">
                  <label for="name">Nama Lengkap *</label>
                  <input
                    type="text"
                    id="name"
                    v-model="complaintData.name"
                    required
                    :class="{ error: errors.name || apiErrors.name }"
                    @blur="validateField('name')"
                    placeholder="Masukkan nama lengkap Anda"
                  >
                  <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
                  <span v-if="apiErrors.name" class="error-message">{{ apiErrors.name[0] }}</span>
                </div>

                <div class="form-group">
                  <label for="email">Alamat Email *</label>
                  <input
                    type="email"
                    id="email"
                    v-model="complaintData.email"
                    required
                    :class="{ error: errors.email || apiErrors.email }"
                    @blur="validateField('email')"
                    placeholder="contoh@email.com"
                  >
                  <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
                  <span v-if="apiErrors.email" class="error-message">{{ apiErrors.email[0] }}</span>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="phone">Nomor Telepon/WhatsApp *</label>
                  <input
                    type="tel"
                    id="phone"
                    v-model="complaintData.phone"
                    required
                    :class="{ error: errors.phone || apiErrors.phone }"
                    @blur="validateField('phone')"
                    placeholder="08xxxxxxxxxx"
                  >
                  <span v-if="errors.phone" class="error-message">{{ errors.phone }}</span>
                  <span v-if="apiErrors.phone" class="error-message">{{ apiErrors.phone[0] }}</span>
                </div>

                <div class="form-group">
                  <label for="category">Kategori Aduan *</label>
                  <select
                    id="category"
                    v-model="complaintData.category"
                    required
                    :class="{ error: errors.category || apiErrors.category }"
                    @change="validateField('category')"
                  >
                    <option value="">Pilih Kategori Aduan</option>
                    <option value="infrastruktur">🏗️ Infrastruktur & Jalan</option>
                    <option value="pendidikan">🎓 Pendidikan</option>
                    <option value="kesehatan">🏥 Kesehatan</option>
                    <option value="lingkungan">🌱 Lingkungan & Kebersihan</option>
                    <option value="pelayanan-publik">🏢 Pelayanan Publik</option>
                    <option value="sosial">👥 Sosial & Kemasyarakatan</option>
                    <option value="ekonomi">💼 Ekonomi & UMKM</option>
                    <option value="pertanian">🌾 Pertanian & Perikanan</option>
                    <option value="lainnya">📋 Lainnya</option>
                  </select>
                  <span v-if="errors.category" class="error-message">{{ errors.category }}</span>
                  <span v-if="apiErrors.category" class="error-message">{{ apiErrors.category[0] }}</span>
                </div>
              </div>
            </div>

            <!-- Complaint Details -->
            <div class="form-section-group">
              <h3 class="group-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-4 4v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14,2 14,8 20,8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                  <polyline points="10,9 9,9 8,9"/>
                </svg>
                Detail Aduan
              </h3>

              <div class="form-group">
                <label for="description">Deskripsi Detail Aduan *</label>
                <textarea
                  id="description"
                  v-model="complaintData.description"
                  rows="6"
                  required
                  :class="{ error: errors.description || apiErrors.description }"
                  @blur="validateField('description')"
                  placeholder="Jelaskan aduan Anda secara detail termasuk:&#10;- Apa yang terjadi?&#10;- Kapan kejadiannya?&#10;- Di mana lokasinya?&#10;- Siapa yang terlibat?&#10;- Dampak yang ditimbulkan"
                  maxlength="1000"
                ></textarea>
                <div class="character-count">{{ complaintData.description.length }}/1000 karakter</div>
                <span v-if="errors.description" class="error-message">{{ errors.description }}</span>
                <span v-if="apiErrors.description" class="error-message">{{ apiErrors.description[0] }}</span>
              </div>
            </div>

            <!-- File Attachments -->
            <div class="form-section-group">
              <h3 class="group-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66L9.64 16.2a2 2 0 0 1-2.83-2.83l8.49-8.49"/>
                </svg>
                Lampiran Pendukung
              </h3>

              <div class="upload-section">
                <div class="upload-group">
                  <label class="upload-label">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21,15 16,10 5,21"/>
                    </svg>
                    Upload Foto
                    <input type="file" @change="handlePhotoUpload" accept="image/*" multiple hidden>
                  </label>
                  <p class="upload-desc">Foto bukti/dokumentasi (JPG, PNG - Max 5MB per file)</p>

                  <div v-if="uploadedPhotos.length > 0" class="uploaded-files">
                    <div v-for="(photo, index) in uploadedPhotos" :key="index" class="file-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21,15 16,10 5,21"/>
                      </svg>
                      <span>{{ photo.name }}</span>
                      <button type="button" @click="removeFile('photo', index)" class="remove-file">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <line x1="18" y1="6" x2="6" y2="18"/>
                          <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="upload-group">
                  <label class="upload-label">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                      <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    Upload Dokumen
                    <input type="file" @change="handleDocumentUpload" accept=".pdf,.doc,.docx" multiple hidden>
                  </label>
                  <p class="upload-desc">Dokumen pendukung (PDF, DOC, DOCX - Max 10MB per file)</p>

                  <div v-if="uploadedDocuments.length > 0" class="uploaded-files">
                    <div v-for="(doc, index) in uploadedDocuments" :key="index" class="file-item">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                      </svg>
                      <span>{{ doc.name }}</span>
                      <button type="button" @click="removeFile('document', index)" class="remove-file">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <line x1="18" y1="6" x2="6" y2="18"/>
                          <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Agreement -->
            <div class="form-section-group">
              <div class="agreement-section">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="agreementAccepted" required>
                  <span class="checkmark"></span>
                  Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan. Saya memahami bahwa pengajuan aduan palsu dapat dikenakan sanksi sesuai peraturan yang berlaku.
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
              <button type="button" @click="resetForm" class="reset-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="1,4 1,10 7,10"/>
                  <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                </svg>
                Reset Form
              </button>
              <button type="submit" class="submit-btn" :disabled="isSubmitting">
                <span v-if="isSubmitting">
                  <div class="loading-spinner"></div>
                  Mengirim...
                </span>
                <span v-else>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                  </svg>
                  Kirim Aduan
                </span>
              </button>
            </div>
          </form>
        </div>

        <!-- Info Sidebar -->
        <aside class="info-sidebar">
          <!-- Information Widget -->
          <div class="info-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4M12 8h.01"/>
              </svg>
              Informasi Penting
            </h3>
            <div class="info-list">
              <div class="info-item">
                <div class="info-icon">✅</div>
                <div class="info-text">Semua field dengan tanda (*) wajib diisi</div>
              </div>
              <div class="info-item">
                <div class="info-icon">⏰</div>
                <div class="info-text">Aduan diproses maksimal 3x24 jam kerja</div>
              </div>
              <div class="info-item">
                <div class="info-icon">📧</div>
                <div class="info-text">Nomor tiket dikirim ke email Anda</div>
              </div>
              <div class="info-item">
                <div class="info-icon">📱</div>
                <div class="info-text">Update status via WhatsApp/SMS</div>
              </div>
              <div class="info-item">
                <div class="info-icon">🔒</div>
                <div class="info-text">Data Anda dijamin kerahasiaan</div>
              </div>
            </div>
          </div>

          <!-- Contact Widget -->
          <div class="info-widget contact-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              Kontak Alternatif
            </h3>
            <div class="contact-list">
              <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
                <div>
                  <strong>Email</strong>
                  <span>aduan@munabarat.go.id</span>
                </div>
              </div>
              <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <div>
                  <strong>Telepon</strong>
                  <span>(0402) 123456</span>
                </div>
              </div>
              <div class="contact-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
                <div>
                  <strong>Alamat</strong>
                  <span>Jl. Poros Kendari - Palu<br>Kabupaten Muna Barat</span>
                </div>
              </div>
              <div class="contact-item whatsapp">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.106"/>
                </svg>
                <div>
                  <strong>WhatsApp</strong>
                  <span>0821-xxxx-xxxx</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Process Timeline -->
          <div class="info-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
              </svg>
              Alur Proses Aduan
            </h3>
            <div class="process-timeline">
              <div class="timeline-item">
                <div class="timeline-step">1</div>
                <div class="timeline-content">
                  <h4>Aduan Diterima</h4>
                  <p>Sistem akan mengirim nomor tiket ke email Anda</p>
                </div>
              </div>
              <div class="timeline-item">
                <div class="timeline-step">2</div>
                <div class="timeline-content">
                  <h4>Verifikasi & Review</h4>
                  <p>Tim akan memverifikasi dan meninjau aduan</p>
                </div>
              </div>
              <div class="timeline-item">
                <div class="timeline-step">3</div>
                <div class="timeline-content">
                  <h4>Proses Tindak Lanjut</h4>
                  <p>Aduan diteruskan ke instansi terkait</p>
                </div>
              </div>
              <div class="timeline-item">
                <div class="timeline-step">4</div>
                <div class="timeline-content">
                  <h4>Penyelesaian</h4>
                  <p>Update status penyelesaian dikirim</p>
                </div>
              </div>
            </div>
          </div>

          <!-- FAQ -->
          <div class="info-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
              </svg>
              FAQ
            </h3>
            <div class="faq-list">
              <details class="faq-item">
                <summary>Berapa lama waktu proses aduan?</summary>
                <p>Aduan akan diproses maksimal 3x24 jam kerja sejak diterima. Untuk kasus darurat akan diprioritaskan lebih cepat.</p>
              </details>
              <details class="faq-item">
                <summary>Apakah data saya aman?</summary>
                <p>Ya, data Anda dijamin kerahasiaan sesuai UU Perlindungan Data Pribadi dan hanya digunakan untuk proses penanganan aduan.</p>
              </details>
              <details class="faq-item">
                <summary>Bagaimana cara melacak status aduan?</summary>
                <p>Anda akan mendapat nomor tiket melalui email. Gunakan nomor tersebut untuk melacak status melalui website atau WhatsApp.</p>
              </details>
            </div>
          </div>

          <!-- Tracking Link -->
          <div class="info-widget">
            <h3 class="widget-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12,6 12,12 16,14"/>
              </svg>
              Lacak Aduan Anda
            </h3>
            <div class="tracking-info">
              <p>Sudah mengirimkan aduan? Lacak status penanganannya sekarang:</p>
              <router-link to="/complaint/tracking" class="tracking-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Lacak Status Aduan
              </router-link>
            </div>
          </div>
        </aside>
      </div>

      <!-- Success Modal -->
      <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
        <div class="success-modal" @click.stop>
          <div class="success-icon">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
              <polyline points="22,4 12,14.01 9,11.01"/>
            </svg>
          </div>
          <h3>Aduan Berhasil Dikirim!</h3>
          <p>Terima kasih atas partisipasi Anda. Nomor tiket aduan:</p>
          <div class="ticket-number">{{ ticketNumber }}</div>
          <p>Kami akan mengirimkan update status ke email dan WhatsApp Anda.</p>
          <button @click="closeSuccessModal" class="modal-btn">Tutup</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import api from '../services/api.js';

export default {
  name: 'Complaint',
  data() {
    return {
      complaintData: {
        name: '',
        nik: '',
        email: '',
        phone: '',
        address: '',
        category: '',
        priority: '',
        subject: '',
        description: '',
        location: ''
      },
      errors: {},
      uploadedPhotos: [],
      uploadedDocuments: [],
      agreementAccepted: false,
      isSubmitting: false,
      showSuccessModal: false,
      ticketNumber: '',
      apiErrors: {},
      stats: {
        total: 0,
        completed: 0,
        inProgress: 0,
        pending: 0
      }
    }
  },
  async mounted() {
    await this.fetchStats();
  },
  methods: {
    async fetchStats() {
      try {
        const response = await api.getComplaintStats();
        if (response.data.success) {
          this.stats = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching complaint stats:', error);
        // Set default values to prevent errors in template
        this.stats = {
          total: 0,
          completed: 0,
          inProgress: 0,
          pending: 0
        };
      }
    },
    validateField(field) {
      this.errors = { ...this.errors };
      delete this.errors[field];

      switch (field) {
        case 'name':
          if (!this.complaintData.name.trim()) {
            this.errors.name = 'Nama lengkap wajib diisi';
          } else if (this.complaintData.name.length < 3) {
            this.errors.name = 'Nama minimal 3 karakter';
          }
          break;
        case 'email':
          if (!this.complaintData.email.trim()) {
            this.errors.email = 'Email wajib diisi';
          } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.complaintData.email)) {
            this.errors.email = 'Format email tidak valid';
          }
          break;
        case 'phone':
          if (!this.complaintData.phone.trim()) {
            this.errors.phone = 'Nomor telepon wajib diisi';
          } else if (!/^(\+62|62|0)8[1-9][0-9]{6,9}$/.test(this.complaintData.phone)) {
            this.errors.phone = 'Nomor telepon tidak valid';
          }
          break;
        case 'category':
          if (!this.complaintData.category) {
            this.errors.category = 'Kategori aduan wajib dipilih';
          }
          break;
        case 'description':
          if (!this.complaintData.description.trim()) {
            this.errors.description = 'Deskripsi aduan wajib diisi';
          } else if (this.complaintData.description.length < 20) {
            this.errors.description = 'Deskripsi minimal 20 karakter';
          }
          break;
      }
    },

    validateAllFields() {
      const requiredFields = ['name', 'email', 'phone', 'category', 'description'];
      requiredFields.forEach(field => {
        this.validateField(field);
      });
      return Object.keys(this.errors).length === 0;
    },

    handleDocumentUpload(event) {
      const files = Array.from(event.target.files);
      files.forEach(file => {
        if (file.size > 10 * 1024 * 1024) {
          alert(`File ${file.name} terlalu besar. Maksimal 10MB.`);
          return;
        }
        this.uploadedDocuments.push(file);
      });
      event.target.value = '';
    },

    handlePhotoUpload(event) {
      const files = Array.from(event.target.files);
      files.forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
          alert(`File ${file.name} terlalu besar. Maksimal 5MB.`);
          return;
        }
        this.uploadedPhotos.push(file);
      });
      event.target.value = '';
    },

    removeFile(type, index) {
      if (type === 'photo') {
        this.uploadedPhotos.splice(index, 1);
      } else {
        this.uploadedDocuments.splice(index, 1);
      }
    },

    generateTicketNumber() {
      const prefix = 'ADU';
      const timestamp = Date.now().toString().slice(-8);
      const random = Math.random().toString(36).substr(2, 4).toUpperCase();
      return `${prefix}${timestamp}${random}`;
    },

    async submitComplaint() {
      if (!this.validateAllFields()) {
        alert('Mohon perbaiki kesalahan pada formulir.');
        return;
      }

      if (!this.agreementAccepted) {
        alert('Anda harus menyetujui pernyataan untuk melanjutkan.');
        return;
      }

      this.isSubmitting = true;
      this.apiErrors = {};

      try {
        // Create FormData object to handle file uploads
        const formData = new FormData();

        // Add text fields
        formData.append('name', this.complaintData.name);
        formData.append('email', this.complaintData.email);
        formData.append('phone', this.complaintData.phone);
        formData.append('category', this.complaintData.category);
        formData.append('description', this.complaintData.description);

        // Add photo files if any
        if (this.uploadedPhotos.length > 0) {
          for (let i = 0; i < this.uploadedPhotos.length; i++) {
            formData.append('photo', this.uploadedPhotos[i]);
          }
        }

        // Add document files if any
        if (this.uploadedDocuments.length > 0) {
          for (let i = 0; i < this.uploadedDocuments.length; i++) {
            formData.append('document', this.uploadedDocuments[i]);
          }
        }

        // Submit to API
        const response = await axios.post('/api/v1/complaints', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        if (response.data.success) {
          this.ticketNumber = response.data.data.id; // Use actual ID from API
          this.showSuccessModal = true;
          this.resetForm();
          // Refresh statistics after submitting a new complaint
          await this.fetchStats();
        } else {
          alert('Terjadi kesalahan saat mengirim aduan. Silakan coba lagi.');
        }
      } catch (error) {
        if (error.response && error.response.data.errors) {
          // Handle validation errors from API
          this.apiErrors = error.response.data.errors;
          let errorMessage = 'Mohon perbaiki kesalahan berikut:\n';
          for (const field in this.apiErrors) {
            errorMessage += `- ${this.apiErrors[field][0]}\n`;
          }
          alert(errorMessage);
        } else {
          alert('Terjadi kesalahan saat mengirim aduan. Silakan coba lagi.');
        }
        console.error('Error submitting complaint:', error);
      } finally {
        this.isSubmitting = false;
      }
    },

    resetForm() {
      this.complaintData = {
        name: '',
        email: '',
        phone: '',
        category: '',
        description: ''
      };
      this.errors = {};
      this.uploadedPhotos = [];
      this.uploadedDocuments = [];
      this.agreementAccepted = false;
      this.apiErrors = {};
    },

    closeSuccessModal() {
      this.showSuccessModal = false;
    }
  }
}
</script>

<style scoped>
.complaint-page {
  background: #f8fffe;
}

/* Hero Section */
.complaint-hero {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 4rem 0 3rem;
  text-align: center;
}

.complaint-hero-content h1 {
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 1rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.complaint-hero-content p {
  font-size: 1.2rem;
  opacity: 0.95;
  margin: 0;
}

/* Container */
.complaint-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Stats Banner */
.stats-banner {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(16, 185, 129, 0.1);
  padding: 2rem;
  margin: -2rem 0 3rem;
  position: relative;
  z-index: 10;
}

.stat-item {
  text-align: center;
  padding: 1rem;
  border-radius: 15px;
  background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  color: #10b981;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: #6b7280;
  font-weight: 600;
  font-size: 0.9rem;
}

/* Content Layout */
.complaint-content {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 3rem;
  align-items: start;
}

/* Form Section */
.form-section {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.section-header {
  text-align: center;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem;
}

.section-header p {
  color: #6b7280;
  margin: 0;
}

/* Progress Steps */
.progress-steps {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 3rem;
  padding: 2rem;
  background: #f9fafb;
  border-radius: 15px;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e5e7eb;
  color: #9ca3af;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  transition: all 0.3s ease;
}

.step.active .step-number {
  background: #10b981;
  color: white;
}

.step-label {
  font-size: 0.85rem;
  color: #6b7280;
  font-weight: 500;
}

.step.active .step-label {
  color: #10b981;
  font-weight: 600;
}

.step-line {
  width: 80px;
  height: 2px;
  background: #e5e7eb;
  margin: 0 1rem;
}

/* Form Styling */
.form-section-group {
  margin-bottom: 3rem;
  padding: 2rem;
  border: 1px solid #f3f4f6;
  border-radius: 15px;
  background: #fafbfc;
}

.group-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.3rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #10b981;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
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

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-group input.error,
.form-group select.error,
.form-group textarea.error {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.character-count {
  text-align: right;
  font-size: 0.8rem;
  color: #9ca3af;
  margin-top: 0.5rem;
}

.error-message {
  display: block;
  color: #dc2626;
  font-size: 0.85rem;
  margin-top: 0.5rem;
  font-weight: 500;
}

/* Upload Section */
.upload-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.upload-group {
  text-align: center;
}

.upload-label {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  border: 2px dashed #10b981;
  border-radius: 15px;
  background: #f0fdf4;
  color: #10b981;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.upload-label:hover {
  background: #ecfdf5;
  border-color: #059669;
}

.upload-desc {
  font-size: 0.85rem;
  color: #6b7280;
  margin-top: 0.5rem;
}

.uploaded-files {
  margin-top: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.file-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.9rem;
}

.file-item span {
  flex: 1;
  text-align: left;
}

.remove-file {
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.remove-file:hover {
  background: #fecaca;
}

/* Agreement */
.agreement-section {
  padding: 1.5rem;
  background: #fffbeb;
  border: 1px solid #f59e0b;
  border-radius: 15px;
}

.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  cursor: pointer;
  font-size: 0.95rem;
  line-height: 1.5;
  color: #92400e;
}

.checkbox-label input {
  width: auto;
  margin: 0;
}

.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid #f59e0b;
  border-radius: 4px;
  position: relative;
  flex-shrink: 0;
  margin-top: 2px;
}

.checkbox-label input:checked + .checkmark {
  background: #f59e0b;
}

.checkbox-label input:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-weight: bold;
  font-size: 0.8rem;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.reset-btn {
  background: #f3f4f6;
  color: #4b5563;
  border: 2px solid #d1d5db;
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.reset-btn:hover {
  background: #e5e7eb;
  border-color: #9ca3af;
}

.submit-btn {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 1rem 3rem;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.submit-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.submit-btn:disabled {
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

/* Sidebar */
.info-sidebar {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.info-widget {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.1);
}

.widget-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
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

/* Info List */
.info-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 10px;
}

.info-icon {
  font-size: 1.2rem;
  flex-shrink: 0;
}

.info-text {
  color: #4b5563;
  font-size: 0.9rem;
  line-height: 1.4;
}

/* Contact Widget */
.contact-widget {
  background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
  color: white;
}

.contact-widget .widget-title::after {
  background: rgba(255,255,255,0.3);
}

.contact-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.contact-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
  padding: 1rem;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.contact-item.whatsapp {
  background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
}

.contact-item div {
  flex: 1;
}

.contact-item strong {
  display: block;
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

.contact-item span {
  font-size: 0.85rem;
  opacity: 0.9;
}

/* Process Timeline */
.process-timeline {
  position: relative;
}

.process-timeline::before {
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
}

.timeline-step {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  flex-shrink: 0;
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

/* Tracking Link */
.tracking-info {
  text-align: center;
  padding: 1rem;
}

.tracking-info p {
  margin: 0 0 1.5rem;
  color: #6b7280;
}

.tracking-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.tracking-link:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

/* Success Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.success-modal {
  background: white;
  border-radius: 20px;
  padding: 3rem;
  max-width: 500px;
  width: 90%;
  text-align: center;
  box-shadow: 0 25px 50px rgba(0,0,0,0.25);
}

.success-icon {
  color: #10b981;
  margin-bottom: 1.5rem;
}

.success-modal h3 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem;
}

.success-modal p {
  color: #6b7280;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.ticket-number {
  font-size: 1.5rem;
  font-weight: 800;
  color: #10b981;
  background: #f0fdf4;
  padding: 1rem;
  border-radius: 10px;
  margin: 1.5rem 0;
  letter-spacing: 2px;
}

.modal-btn {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 1rem;
  transition: all 0.3s ease;
}

.modal-btn:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

/* Responsive Design */
@media (max-width: 1200px) {
  .complaint-content {
    grid-template-columns: 1fr 350px;
    gap: 2rem;
  }
}

@media (max-width: 992px) {
  .complaint-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .info-sidebar {
    order: -1;
  }

  .stats-banner {
    grid-template-columns: repeat(2, 1fr);
  }

  .upload-section {
    grid-template-columns: 1fr;
  }

  .form-section-group {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .complaint-hero-content h1 {
    font-size: 2.5rem;
  }

  .complaint-container {
    padding: 0 1rem;
  }

  .stats-banner {
    padding: 1.5rem;
    margin: -1.5rem 0 2rem;
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .form-section {
    padding: 1.5rem;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .progress-steps {
    padding: 1.5rem;
    flex-direction: column;
    gap: 1rem;
  }

  .step-line {
    width: 2px;
    height: 40px;
    margin: 0;
  }

  .form-actions {
    flex-direction: column;
    gap: 1rem;
  }

  .info-widget {
    padding: 1.5rem;
  }
}

@media (max-width: 480px) {
  .complaint-hero-content h1 {
    font-size: 2rem;
  }

  .section-header h2 {
    font-size: 1.5rem;
  }

  .group-title {
    font-size: 1.1rem;
  }

  .success-modal {
    padding: 2rem;
  }

  .success-modal h3 {
    font-size: 1.5rem;
  }

  .ticket-number {
    font-size: 1.2rem;
  }
}

/* Tracking Link */
.tracking-info {
  text-align: center;
  padding: 1rem;
}

.tracking-info p {
  margin: 0 0 1.5rem;
  color: #6b7280;
}

.tracking-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.tracking-link:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}
</style>
