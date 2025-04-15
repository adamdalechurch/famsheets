<template>
<section class="tab-components">
  <div class="container-fluid">
    <div class="title-wrapper pt-30">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="title-logo">
            <a href="/">
              <Logo />
            </a>
          </div>
        </div>
        <!-- end col -->
        <!-- end col -->
      </div>
      <!-- end row -->
    </div>
      <router-view></router-view>
    </div>
  <!-- end container -->
</section>
</template>

<script>

import Logo from '@/components/common/Logo.vue';
export default {
  name: 'Auth',
  components: {
    Logo,
  },
};
</script>

<style>
.title-logo{
  margin: 20px auto;
  text-align: center;
}

.auth {
  margin: 0 auto;
}
</style>