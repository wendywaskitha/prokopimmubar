import api from './api';

class SettingsService {
  /**
   * Get all settings
   * @returns {Promise}
   */
  getAll() {
    return api.getSettings();
  }

  /**
   * Get specific setting by key
   * @param {string} key
   * @returns {Promise}
   */
  getByKey(key) {
    return api.getSettingByKey(key);
  }
}

export default new SettingsService();