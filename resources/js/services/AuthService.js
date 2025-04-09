// src/services/AuthService.js
import axios from '@/util/axios';

export default {
  async getCSRF() {
    await axios.get('/sanctum/csrf-cookie');
  },

  async login(email, password) {
    await this.getCSRF();
    axios.post('/api/login', { email, password })
      .then((res) => {
        // if (res.data.success) {
          // document.cookie = `XSRF-TOKEN=${axios.defaults.xsrfCookieName}`;
          location.replace('/');
        //}
      })
      .catch((err) => {
        console.error(err);
      });
  },

  async logout() {
    await axios.post('/api/logout');
  },

  async register(data) {
    await this.getCSRF();
    await axios.post('/api/register', data);
  },

  async getUser() {
    const res = await axios.get('/api/user');
    return res.data;
  },
};
