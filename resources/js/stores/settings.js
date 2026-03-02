import { defineStore } from 'pinia'
import SettingsService from '../services/SettingsService'

export const useSettingsStore = defineStore('settings', {
  state: () => ({
    settings: {
      app_name: 'WARTA DAERAH',
      app_description: 'Portal informasi resmi yang menyajikan berita terkini, artikel berkualitas, dan liputan mendalam tentang perkembangan Kabupaten Muna Barat.',
      app_address: 'Kompleks Bumi Praja Laworo, Kabupaten Muna Barat, Sulawesi Tenggara',
      app_email: 'info@munabarat.go.id',
      app_phone: '(0402) 123456',
      facebook_url: '#',
      twitter_url: '#',
      instagram_url: '#',
      youtube_url: '#',
      app_logo: null,
      app_favicon: null
    },
    isLoading: false,
    error: null
  }),

  getters: {
    appName: (state) => state.settings.app_name,
    appDescription: (state) => state.settings.app_description,
    appAddress: (state) => state.settings.app_address,
    appEmail: (state) => state.settings.app_email,
    appPhone: (state) => state.settings.app_phone,
    facebookUrl: (state) => state.settings.facebook_url,
    twitterUrl: (state) => state.settings.twitter_url,
    instagramUrl: (state) => state.settings.instagram_url,
    youtubeUrl: (state) => state.settings.youtube_url,
    appLogo: (state) => state.settings.app_logo,
    appFavicon: (state) => state.settings.app_favicon
  },

  actions: {
    async fetchSettings() {
      if (this.isLoading) return

      this.isLoading = true
      this.error = null

      try {
        const response = await SettingsService.getAll()
        if (response && response.data && response.data.success) {
          this.settings = { ...this.settings, ...response.data.data }
        } else {
          throw new Error('Invalid response format')
        }
      } catch (error) {
        this.error = error.message
        console.error('Failed to fetch settings:', error)
      } finally {
        this.isLoading = false
      }
    }
  }
})