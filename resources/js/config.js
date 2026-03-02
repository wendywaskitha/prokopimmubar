/**
 * Application Configuration
 * 
 * This file provides centralized configuration for the Vue.js application.
 * It automatically detects the environment and sets the appropriate base URL.
 */

const config = {
  // Detect base URL from current window location (works in production)
  // Falls back to localhost for local development
  baseUrl: window.location.origin || 'http://localhost:8000',
  
  // Storage URL - uses relative path for production, absolute for local dev
  storageUrl: window.location.origin 
    ? window.location.origin + '/storage'
    : 'http://localhost:8000/storage',
  
  // API Base URL
  apiBaseUrl: window.location.origin 
    ? window.location.origin + '/api/v1'
    : 'http://localhost:8000/api/v1',
  
  // App name from Vite define or default
  appName: import.meta.env.VITE_APP_NAME || 'Liwumokesanews',
  
  // Debug mode
  debug: import.meta.env.DEV || false,
  
  /**
   * Get full storage URL for an image path
   * @param {string} imagePath - Relative path to the image
   * @returns {string} Full URL to the image
   */
  getStorageUrl(imagePath) {
    if (!imagePath) return '';
    
    // If already a full URL, return as is
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }
    
    // If starts with /storage/, use current origin
    if (imagePath.startsWith('/storage/')) {
      return window.location.origin + imagePath;
    }
    
    // Otherwise, prepend storage URL
    return this.storageUrl + '/' + imagePath.replace(/^\/+/, '');
  },
  
  /**
   * Get storage URL for paths that may already be in /storage/ format
   * This is useful for template bindings that use `/storage/${path}`
   * @param {string} imagePath - Path that may start with /storage/
   * @returns {string} Full URL to the image
   */
  resolveStorageUrl(imagePath) {
    if (!imagePath) return '';
    
    // If already a full URL, return as is
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }
    
    // If starts with /storage/, prepend current origin
    if (imagePath.startsWith('/storage/')) {
      return window.location.origin + imagePath;
    }
    
    // Otherwise, treat as regular path
    return this.getStorageUrl(imagePath);
  },
  
  /**
   * Get asset URL (for compiled assets like images, CSS, JS)
   * @param {string} assetPath - Path to the asset
   * @returns {string} Full URL to the asset
   */
  getAssetUrl(assetPath) {
    if (!assetPath) return '';
    
    // If already a full URL, return as is
    if (assetPath.startsWith('http://') || assetPath.startsWith('https://')) {
      return assetPath;
    }
    
    // For relative paths, use current origin
    return window.location.origin + '/' + assetPath.replace(/^\/+/, '');
  }
};

export default config;
