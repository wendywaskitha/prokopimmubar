import api from './api'

export default {
  // Get all banners
  async getAll() {
    try {
      const response = await api.getBanners()
      return response
    } catch (error) {
      throw error
    }
  },

  // Get banners by position
  async getByPosition(position) {
    try {
      const response = await api.getBannersByPosition(position)
      return response
    } catch (error) {
      throw error
    }
  },

  // Get OPD head greetings banners
  async getOpdHeadGreetings() {
    try {
      const response = await api.getOpdHeadGreetings()
      return response
    } catch (error) {
      throw error
    }
  },

  // Get banner by ID
  async getById(id) {
    try {
      const response = await api.getBannerById(id)
      return response
    } catch (error) {
      throw error
    }
  },

  // Track banner click
  async trackClick(id) {
    try {
      const response = await api.trackBannerClick(id)
      return response
    } catch (error) {
      throw error
    }
  }
}