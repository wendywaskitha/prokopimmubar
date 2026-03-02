<template>
  <footer>
    <!-- Call to Action Section -->
    <div class="cta-footer">
      <div class="cta-overlay"></div>
      <div class="cta-content">
        <h2>Bergabunglah Sebagai Kontributor Warta Daerah</h2>
        <p>Kami mencari storyteller berbakat yang ingin berkarya dengan kami untuk berbagi cerita-cerita inspiratif dari Muna Barat dan sekitarnya.</p>
        <button class="cta-button">Bergabung Sekarang</button>
      </div>
    </div>

    <!-- Main Footer Content -->
    <div class="footer-main">
      <div class="footer-content">
        <!-- Brand Section -->
        <div class="footer-brand">
          <div class="footer-logo">
            <div class="logo-icon" v-if="!settings.app_logo">📰</div>
            <img v-else :src="getLogoUrl()" class="logo-icon" alt="Logo">
            <div class="logo-text">
              <span class="logo-main">{{ settings.app_name || 'WARTA DAERAH' }}</span>
              <span class="logo-sub">MUNA BARAT</span>
            </div>
          </div>
          <p class="footer-description">{{ settings.app_description || 'Portal informasi resmi yang menyajikan berita terkini, artikel berkualitas, dan liputan mendalam tentang perkembangan Kabupaten Muna Barat.' }}</p>
          <div class="social-media-links">
            <a :href="settings.facebook_url || '#'" class="social-icon facebook" title="Facebook">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a :href="settings.twitter_url || '#'" class="social-icon twitter" title="Twitter">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
              </svg>
            </a>
            <a :href="settings.instagram_url || '#'" class="social-icon instagram" title="Instagram">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </a>
            <a :href="settings.youtube_url || '#'" class="social-icon youtube" title="YouTube">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Links Sections -->
        <div class="footer-links">
          <div class="footer-section">
            <h4>Portal Kami</h4>
            <ul>
              <li><a href="/">Beranda</a></li>
              <li><a href="/news">Berita</a></li>
              <li><a href="/gallery">Galeri</a></li>
              <li><a href="/social-media">Media Sosial</a></li>
              <li><a href="/complaint">Aduan Masyarakat</a></li>
            </ul>
          </div>

          <div class="footer-section">
            <h4>Informasi Daerah</h4>
            <ul>
              <li><a href="#">Profil Daerah</a></li>
              <li><a href="#">Pemerintahan</a></li>
              <li><a href="#">Statistik</a></li>
              <li><a href="#">Potensi Daerah</a></li>
              <li><a href="#">APBD</a></li>
            </ul>
          </div>

          <div class="footer-section">
            <h4>Layanan Publik</h4>
            <ul>
              <li><a href="#">Layanan Online</a></li>
              <li><a href="#">Pengaduan</a></li>
              <li><a href="#">Perizinan</a></li>
              <li><a href="#">E-Government</a></li>
              <li><a href="#">Hotline Service</a></li>
            </ul>
          </div>

          <div class="footer-section">
            <h4>Legal & Kebijakan</h4>
            <ul>
              <li><a href="#">Kebijakan Privasi</a></li>
              <li><a href="#">Syarat & Ketentuan</a></li>
              <li><a href="#">Disclaimer</a></li>
              <li><a href="#">Kontak Redaksi</a></li>
              <li><a href="#">Pedoman Media</a></li>
            </ul>
          </div>
        </div>

        <!-- Contact Section -->
        <div class="footer-contact">
          <h4>Hubungi Kami</h4>
          <div class="contact-info">
            <div class="contact-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>{{ settings.app_address || 'Kompleks Bumi Praja Laworo, Kabupaten Muna Barat, Sulawesi Tenggara' }}</span>
            </div>
            <div class="contact-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
              <span>{{ settings.app_phone || '(0402) 123456' }}</span>
            </div>
            <div class="contact-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
              <span>{{ settings.app_email || 'info@munabarat.go.id' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
      <div class="footer-bottom-content">
        <div class="copyright">
          <p>&copy; {{ new Date().getFullYear() }} {{ settings.app_name || 'Pemerintah Kabupaten Muna Barat' }}. Seluruh hak dilindungi undang-undang.</p>
        </div>
        <div class="footer-bottom-links">
          <a href="#">Peta Situs</a>
          <a href="#">Aksesibilitas</a>
          <a href="#">RSS Feed</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useSettingsStore } from '../stores/settings';
import config from '../config';

export default {
  name: 'Footer',
  computed: {
    ...mapState(useSettingsStore, ['settings'])
  },
  async mounted() {
    await this.fetchSettings();
  },
  methods: {
    ...mapActions(useSettingsStore, ['fetchSettings']),
    getLogoUrl() {
      return config.resolveStorageUrl(this.settings.app_logo);
    }
  }
}
</script>

<style scoped>
footer {
  margin-top: auto;
}

/* CTA Footer Section */
.cta-footer {
  position: relative;
  background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
  background-image: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  color: white;
  text-align: center;
  padding: 5rem 2rem;
  overflow: hidden;
}

.cta-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(31, 41, 55, 0.9) 0%, rgba(17, 24, 39, 0.95) 100%);
}

.cta-content {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
  z-index: 1;
}

.cta-content h2 {
  font-size: 3rem;
  font-weight: 800;
  margin: 0 0 1.5rem;
  line-height: 1.2;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.cta-content p {
  font-size: 1.2rem;
  margin-bottom: 2.5rem;
  opacity: 0.95;
  line-height: 1.6;
}

.cta-button {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 1.25rem 3rem;
  border-radius: 50px;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.cta-button:hover {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
}

/* Main Footer */
.footer-main {
  background: #1f2937;
  color: #e5e7eb;
  padding: 4rem 0 2rem;
}

.footer-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 4rem;
  align-items: start;
}

/* Brand Section */
.footer-brand {
  max-width: 350px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.logo-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  background-color: transparent;
  object-fit: contain;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-main {
  font-size: 1.3rem;
  font-weight: 800;
  color: white;
  line-height: 1;
  letter-spacing: 0.5px;
}

.logo-sub {
  font-size: 0.9rem;
  font-weight: 500;
  color: #10b981;
  line-height: 1;
  margin-top: 4px;
}

.footer-description {
  color: #9ca3af;
  line-height: 1.6;
  margin-bottom: 2rem;
  font-size: 0.95rem;
}

/* Social Media */
.social-media-links {
  display: flex;
  gap: 1rem;
}

.social-icon {
  width: 45px;
  height: 45px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  text-decoration: none;
  border: 1px solid #374151;
}

.social-icon.facebook {
  color: #1877f2;
  background: rgba(24, 119, 242, 0.1);
}

.social-icon.twitter {
  color: #1da1f2;
  background: rgba(29, 161, 242, 0.1);
}

.social-icon.instagram {
  color: #e4405f;
  background: rgba(228, 64, 95, 0.1);
}

.social-icon.youtube {
  color: #ff0000;
  background: rgba(255, 0, 0, 0.1);
}

.social-icon:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  border-color: currentColor;
}

/* Footer Links */
.footer-links {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
}

.footer-section h4 {
  color: white;
  font-weight: 600;
  font-size: 1.1rem;
  margin: 0 0 1.5rem;
  position: relative;
}

.footer-section h4::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 30px;
  height: 2px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 1px;
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section ul li {
  margin-bottom: 0.75rem;
}

.footer-section ul li a {
  color: #9ca3af;
  text-decoration: none;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  display: block;
  padding: 0.25rem 0;
}

.footer-section ul li a:hover {
  color: #10b981;
  padding-left: 0.5rem;
}

/* Contact Section */
.footer-contact h4 {
  color: white;
  font-weight: 600;
  font-size: 1.1rem;
  margin: 0 0 1.5rem;
  position: relative;
}

.footer-contact h4::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 30px;
  height: 2px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 1px;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  color: #9ca3af;
  font-size: 0.9rem;
  line-height: 1.5;
}

.contact-item svg {
  color: #10b981;
  margin-top: 2px;
  flex-shrink: 0;
}

/* Footer Bottom */
.footer-bottom {
  background: #111827;
  border-top: 1px solid #374151;
  padding: 1.5rem 0;
}

.footer-bottom-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.copyright p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
}

.footer-bottom-links {
  display: flex;
  gap: 2rem;
}

.footer-bottom-links a {
  color: #6b7280;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
  color: #10b981;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .footer-content {
    grid-template-columns: 1fr 1.5fr 1fr;
    gap: 3rem;
  }

  .footer-links {
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }
}

@media (max-width: 992px) {
  .cta-content h2 {
    font-size: 2.5rem;
  }

  .footer-content {
    grid-template-columns: 1fr;
    gap: 3rem;
    text-align: center;
  }

  .footer-links {
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
  }

  .footer-brand {
    max-width: none;
  }

  .social-media-links {
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .cta-footer {
    padding: 3rem 1rem;
    background-attachment: scroll;
  }

  .cta-content h2 {
    font-size: 2rem;
  }

  .footer-main {
    padding: 3rem 0 2rem;
  }

  .footer-content {
    padding: 0 1rem;
    gap: 2.5rem;
  }

  .footer-links {
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }

  .footer-bottom-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
    padding: 0 1rem;
  }

  .footer-bottom-links {
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .cta-content h2 {
    font-size: 1.7rem;
  }

  .cta-content p {
    font-size: 1rem;
  }

  .cta-button {
    padding: 1rem 2rem;
    font-size: 1rem;
  }

  .footer-links {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .footer-section h4::after {
    left: 50%;
    transform: translateX(-50%);
  }

  .footer-contact h4::after {
    left: 50%;
    transform: translateX(-50%);
  }
}
</style>
