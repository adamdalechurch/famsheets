<template>
  <div class="col-lg-6">
    <div class="password-reset-wrapper">
      <div class="form-wrapper">
        <h6 class="mb-15">Reset Password</h6>
        <p class="text-sm mb-25">
          Enter your email address to receive a password reset link.
        </p>
        <form @submit.prevent="requestPasswordReset">
          <div class="row">
            <div class="col-12">
              <div class="input-style-1">
                <label>Email</label>
                <input type="email" v-model="email" required placeholder="Email" />
              </div>
            </div>
            <div class="col-12">
              <div class="button-group d-flex justify-content-center flex-wrap">
                <button type="submit" class="main-btn primary-btn btn-hover w-100 text-center">
                  Send Reset Link
                </button>
              </div>
            </div>
          </div>
        </form>
        <p class="text-sm text-medium text-dark text-center">
          Remembered your password?
          <router-link to="/signin">Sign in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PasswordReset',
  data() {
    return {
      email: '',
    };
  },
  methods: {
    async requestPasswordReset() {
      try {
        const response = await axios.post('/api/password-reset', {
          email: this.email,
        });
        alert('Password reset link sent! Please check your email.');
      } catch (error) {
        alert('Failed to send password reset link.');
        console.error(error);
      }
    },
  },
};
</script>
