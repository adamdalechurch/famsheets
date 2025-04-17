<template>
  <div class="col-lg-6 auth">
    <div class="signup-wrapper">
      <div class="form-wrapper">
        <h6 class="mb-15">Sign Up Form</h6>
        <p class="text-sm mb-25">
          Join us to start creating the best possible user experience for your customers.
        </p>
        <form @submit.prevent="signUp">
          <div class="row">
            <div class="col-12">
              <div class="input-style-1">
                <label>Name</label>
                <input type="text" v-model="name" required placeholder="Full Name" />
              </div>
            </div>
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
            <div class="col-12">
              <div class="button-group d-flex justify-content-center flex-wrap">
                <button type="submit" class="main-btn primary-btn btn-hover w-100 text-center">
                  Sign Up
                </button>
              </div>
            </div>
          </div>
        </form>
        <p class="text-sm text-medium text-dark text-center">
          Already have an account?
          <router-link to="/signin">Sign in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'SignUp',
  data() {
    return {
      name: '',
      email: '',
      password: '',
    };
  },
  methods: {
    async signUp() {
      try {
        const response = await axios.post('/api/signup', {
          name: this.name,
          email: this.email,
          password: this.password,
        });
        alert('Account created successfully! Please sign in.');
        this.$router.push('/signin');
      } catch (error) {
        alert('Sign up failed.');
        console.error(error);
      }
    },
  },
};
</script>
