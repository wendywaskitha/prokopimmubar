import axios from 'axios';

// Use relative URL for production, fallback to localhost for development
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api/v1';

// Simple in-memory cache
const cache = new Map();
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Cache helper functions
function getCacheKey(endpoint, params = {}) {
  const paramStr = new URLSearchParams(params).toString();
  return `${endpoint}?${paramStr}`;
}

function getCachedData(key) {
  const cached = cache.get(key);
  if (cached && Date.now() - cached.timestamp < CACHE_DURATION) {
    return cached.data;
  }
  cache.delete(key);
  return null;
}

function setCachedData(key, data) {
  cache.set(key, {
    data,
    timestamp: Date.now()
  });
}

function clearCache() {
  cache.clear();
}

export default {
  // Hero API
  async getHeroes(useCache = true) {
    const cacheKey = getCacheKey('/heroes');
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/heroes');
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  getHeroById(id) {
    return apiClient.get(`/heroes/${id}`);
  },

  // News API
  async getNews(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/news', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/news', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  getNewsBySlug(slug) {
    return apiClient.get(`/news/${slug}`);
  },

  getNewsByCategory(category) {
    return apiClient.get(`/news/category/${category}`);
  },

  async getPopularNews(useCache = true) {
    const cacheKey = getCacheKey('/news/popular');
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/news/popular');
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  // Gallery API
  async getGalleries(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/galleries', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/galleries', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  getGalleryById(id) {
    return apiClient.get(`/galleries/${id}`);
  },

  // Social Media API
  async getSocialMedia(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/social-media', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/social-media', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  getSocialMediaById(id) {
    return apiClient.get(`/social-media/${id}`);
  },

  getSocialMediaByPlatform(platform) {
    return apiClient.get(`/social-media/platform/${platform}`);
  },

  // Complaint API
  submitComplaint(data) {
    return apiClient.post('/complaints', data);
  },

  getComplaints(params = {}) {
    return apiClient.get('/complaints', { params });
  },

  getComplaintById(id) {
    return apiClient.get(`/complaints/${id}`);
  },

  updateComplaintStatus(id, data) {
    return apiClient.put(`/complaints/${id}`, data);
  },

  async getComplaintStats(useCache = true) {
    const cacheKey = getCacheKey('/complaints-stats');
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/complaints-stats');
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  // Work Program API
  async getWorkPrograms(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/work-programs', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/work-programs', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  getWorkProgramById(id) {
    return apiClient.get(`/work-programs/${id}`);
  },

  // Banner API
  async getBanners(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/banners', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/banners', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  async getBannersByPosition(position, useCache = true) {
    const cacheKey = getCacheKey(`/banners/position/${position}`);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get(`/banners/position/${position}`);
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  async getOpdHeadGreetings(useCache = true) {
    const cacheKey = getCacheKey('/banners/opd-head-greetings');
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/banners/opd-head-greetings');
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  getBannerById(id) {
    return apiClient.get(`/banners/${id}`);
  },

  trackBannerClick(id) {
    return apiClient.post(`/banners/${id}/click`);
  },

  // Agenda API
  async getAgendas(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/agendas', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/agendas', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  getAgendaById(id) {
    return apiClient.get(`/agendas/${id}`);
  },

  async getUpcomingAgendas(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/agendas-upcoming', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/agendas-upcoming', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  async getOngoingAgendas(params = {}, useCache = true) {
    const cacheKey = getCacheKey('/agendas-ongoing', params);
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/agendas-ongoing', { params });
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },

  // Category API
  async getCategories(useCache = true) {
    const cacheKey = getCacheKey('/categories');
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/categories');
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },
  
  // Settings API
  async getSettings(useCache = true) {
    const cacheKey = getCacheKey('/settings');
    if (useCache) {
      const cached = getCachedData(cacheKey);
      if (cached) return cached;
    }
    
    const response = await apiClient.get('/settings');
    if (useCache) {
      setCachedData(cacheKey, response);
    }
    return response;
  },
  
  getSettingByKey(key) {
    return apiClient.get(`/settings/${key}`);
  },
  
  // Clear cache when needed
  clearCache
}
