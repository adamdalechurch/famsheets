<template>
  <div>
    <nav>
      <router-link to="/signin">Sign In</router-link>
      <router-link to="/signup">Sign Up</router-link>
      <router-link to="/password-reset">Reset Password</router-link>
      <router-link to="/transactions">Transactions</router-link>
    </nav>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style scoped>
nav {
  margin-bottom: 20px;
}
nav a {
  margin-right: 15px;
}
</style>
