<template>
  <div class="col-lg-6">
    <div class="signin-wrapper">
      <div class="form-wrapper">
        <h6 class="mb-15">Sign In Form</h6>
        <p class="text-sm mb-25">
          Start creating the best possible user experience for your customers.
        </p>
        <form @submit.prevent="signIn">
          <div class="row">
            <div class="col-12">
              <div class="input-style-1">
                <label>Email</label>
                <input type="email" v-model="email" required placeholder="Email" />
              </div>
            </div>
            <div class="col-12">
              <div class="input-style-1">
                <label>Password</label>
                <input type="password" v-model="password" required placeholder="Password" />
              </div>
            </div>
            <div class="col-xxl-6 col-lg-12 col-md-6">
              <div class="form-check checkbox-style mb-30">
                <input class="form-check-input" type="checkbox" v-model="rememberMe" id="checkbox-remember" />
                <label class="form-check-label" for="checkbox-remember">Remember me next time</label>
              </div>
            </div>
            <div class="col-xxl-6 col-lg-12 col-md-6">
              <div class="text-start text-md-end text-lg-start text-xxl-end mb-30">
                <router-link to="/reset-password" class="hover-underline">Forgot Password?</router-link>
              </div>
            </div>
            <div class="col-12">
              <div class="button-group d-flex justify-content-center flex-wrap">
                <button type="submit" class="main-btn primary-btn btn-hover w-100 text-center">Sign In</button>
              </div>
            </div>
          </div>
        </form>
        <div class="singin-option pt-40">
          <p class="text-sm text-medium text-center text-gray">Easy Sign In With</p>
          <div class="button-group pt-40 pb-40 d-flex justify-content-center flex-wrap">
            <button class="main-btn primary-btn-outline m-2"><i class="lni lni-facebook-fill mr-10"></i>Facebook</button>
            <button class="main-btn danger-btn-outline m-2"><i class="lni lni-google mr-10"></i>Google</button>
          </div>
          <p class="text-sm text-medium text-dark text-center">
            Don’t have an account yet?
            <router-link to="/signup">Create an account</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'SignIn',
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
    };
  },
  methods: {
    async signIn() {
      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password,
          remember: this.rememberMe,
        });
        alert('Logged in!');
        this.$router.push('/transactions');
      } catch (error) {
        alert('Login failed.');
        console.error(error);
      }
    },
  },
};
</script>
