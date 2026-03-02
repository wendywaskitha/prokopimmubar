<template>
  <header>
    <div class="header-content">
      <div class="navbar">
        <!-- Logo Section -->
        <div class="logo">
          <div class="logo-icon" v-if="!settings.app_logo">📰</div>
          <img v-else :src="`/storage/${settings.app_logo}`" class="logo-icon" alt="Logo">
          <div class="logo-text">
            <span class="logo-main">{{ settings.app_name || 'WARTA DAERAH' }}</span>
            <span class="logo-sub">MUNA BARAT</span>
          </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="main-nav" :class="{ 'mobile-menu-open': isMobileMenuOpen }">
          <ul class="nav-menu">
            <li class="nav-item">
              <router-link to="/" @click="closeMobileMenu" class="nav-link">Beranda</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/news" @click="closeMobileMenu" class="nav-link">Berita</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/agenda" @click="closeMobileMenu" class="nav-link">Agenda</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/gallery" @click="closeMobileMenu" class="nav-link">Galeri</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/social-media" @click="closeMobileMenu" class="nav-link">Media Sosial</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/complaint" @click="closeMobileMenu" class="nav-link">Aduan</router-link>
            </li>
          </ul>
        </nav>

        <!-- Right Side Actions -->
        <div class="header-actions">
          <!-- Search Bar -->
          <div class="search-container" :class="{ 'search-active': isSearchActive }">
            <button class="search-toggle" @click="toggleSearch" v-show="!isSearchActive">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </button>
            <div class="search-bar" v-show="isSearchActive">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Cari berita..."
                @keyup.enter="performSearch"
                @blur="closeSearch"
                ref="searchInput"
              />
              <button @click="performSearch" class="search-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button class="subscribe-btn">Berlangganan</button>
            <button class="login-btn" @click="redirectToAdminLogin">Masuk</button>
          </div>

          <!-- Mobile Menu Toggle -->
          <button class="mobile-menu-toggle" @click="toggleMobileMenu" :class="{ 'active': isMobileMenuOpen }">
            <span class="hamburger"></span>
            <span class="hamburger"></span>
            <span class="hamburger"></span>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Search Bar -->
    <div class="mobile-search" v-show="isMobileMenuOpen">
      <div class="search-bar">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Cari berita..."
          @keyup.enter="performSearch"
        />
        <button @click="performSearch" class="search-btn">Cari</button>
      </div>
    </div>
  </header>
</template>

<script>
import { mapState, mapActions } from 'pinia';
import { useSettingsStore } from '../stores/settings';

export default {
  name: 'Navbar',
  data() {
    return {
      isMobileMenuOpen: false,
      isSearchActive: false,
      searchQuery: ''
    }
  },
  computed: {
    ...mapState(useSettingsStore, ['settings'])
  },
  async mounted() {
    await this.fetchSettings();
  },
  methods: {
    ...mapActions(useSettingsStore, ['fetchSettings']),
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    },
    closeMobileMenu() {
      this.isMobileMenuOpen = false;
    },
    toggleSearch() {
      this.isSearchActive = !this.isSearchActive;
      if (this.isSearchActive) {
        this.$nextTick(() => {
          this.$refs.searchInput?.focus();
        });
      }
    },
    closeSearch() {
      setTimeout(() => {
        this.isSearchActive = false;
      }, 200);
    },
    performSearch() {
      if (this.searchQuery.trim() !== '') {
        this.$router.push({ name: 'News', query: { search: this.searchQuery } });
        this.searchQuery = '';
        this.isSearchActive = false;
        this.closeMobileMenu();
      }
    },
    redirectToAdminLogin() {
      // Redirect to Filament admin login page
      window.location.href = '/admin/login';
    }
  }
}
</script>

<style scoped>
header {
  background: white;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
  margin-bottom: 0;
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem;
}

.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
}

/* Logo Styles */
.logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: #1f2937;
}

.logo-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  background-color: transparent;
  object-fit: contain;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-main {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1f2937;
  line-height: 1;
  letter-spacing: 0.5px;
}

.logo-sub {
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  line-height: 1;
  margin-top: 2px;
}

/* Navigation Styles */
.main-nav {
  flex: 1;
  display: flex;
  justify-content: center;
}

.nav-menu {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  gap: 2rem;
  align-items: center;
}

.nav-item {
  position: relative;
}

.nav-link {
  text-decoration: none;
  color: #4b5563;
  font-weight: 500;
  font-size: 0.95rem;
  padding: 0.5rem 0;
  transition: all 0.3s ease;
  position: relative;
}

.nav-link:hover {
  color: #10b981;
}

.nav-link.router-link-exact-active {
  color: #10b981;
  font-weight: 600;
}

.nav-link.router-link-exact-active::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 1px;
}

/* Header Actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* Search Styles */
.search-container {
  position: relative;
}

.search-toggle {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.search-toggle:hover {
  background: #f3f4f6;
  color: #10b981;
}

.search-bar {
  display: flex;
  align-items: center;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 25px;
  padding: 0.25rem;
  min-width: 250px;
  transition: all 0.3s ease;
}

.search-bar:focus-within {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-bar input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 0.5rem 1rem;
  outline: none;
  font-size: 0.9rem;
  color: #1f2937;
}

.search-bar input::placeholder {
  color: #9ca3af;
}

.search-btn {
  background: #10b981;
  color: white;
  border: none;
  border-radius: 20px;
  padding: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 36px;
  height: 36px;
}

.search-btn:hover {
  background: #059669;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.subscribe-btn {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.subscribe-btn:hover {
  background: linear-gradient(135deg, #059669, #047857);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.login-btn {
  background: white;
  color: #4b5563;
  border: 1px solid #d1d5db;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.login-btn:hover {
  background: #f9fafb;
  border-color: #10b981;
  color: #10b981;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  gap: 4px;
}

.hamburger {
  width: 24px;
  height: 2px;
  background-color: #4b5563;
  transition: all 0.3s ease;
  border-radius: 1px;
}

.mobile-menu-toggle.active .hamburger:nth-child(1) {
  transform: rotate(45deg) translate(6px, 6px);
}

.mobile-menu-toggle.active .hamburger:nth-child(2) {
  opacity: 0;
}

.mobile-menu-toggle.active .hamburger:nth-child(3) {
  transform: rotate(-45deg) translate(6px, -6px);
}

/* Mobile Search */
.mobile-search {
  padding: 1rem;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.mobile-search .search-bar {
  border-radius: 10px;
  min-width: auto;
}

.mobile-search .search-btn {
  border-radius: 8px;
  min-width: 60px;
  font-size: 0.9rem;
  font-weight: 600;
}

/* Mobile Responsive */
@media (max-width: 1024px) {
  .nav-menu {
    gap: 1.5rem;
  }

  .action-buttons {
    gap: 0.5rem;
  }

  .subscribe-btn,
  .login-btn {
    padding: 0.6rem 1.2rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 768px) {
  .navbar {
    height: 60px;
  }

  .logo-text {
    display: none;
  }

  .main-nav {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-top: 1px solid #e5e7eb;
  }

  .main-nav.mobile-menu-open {
    display: block;
  }

  .nav-menu {
    flex-direction: column;
    align-items: flex-start;
    padding: 1rem;
    gap: 0;
  }

  .nav-item {
    width: 100%;
  }

  .nav-link {
    display: block;
    padding: 1rem;
    width: 100%;
    border-bottom: 1px solid #f3f4f6;
  }

  .nav-link.router-link-exact-active::after {
    bottom: 0;
    left: 1rem;
    right: 1rem;
  }

  .search-container {
    display: none;
  }

  .action-buttons {
    display: none;
  }

  .mobile-menu-toggle {
    display: flex;
  }
}

@media (max-width: 480px) {
  .header-content {
    padding: 0 0.75rem;
  }

  .navbar {
    height: 55px;
  }

  .logo-icon {
    width: 35px;
    height: 35px;
    font-size: 1rem;
  }
}
</style>
