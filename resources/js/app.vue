<template>
  <div id="app">
    <!-- Preloader -->
    <div id="preloader">
      <div class="spinner"></div>
    </div>

    <!-- Overlay -->
    <div class="overlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar-nav-wrapper">
      <div class="navbar-logo">
        <router-link to="/">
          <img src="assets/images/logo/logo.svg" />
        </router-link>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li class="nav-item">
            <router-link to="/app">
              <span class="icon"><i class="lni lni-dashboard"></i></span>
              <span class="text">Dashboard</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/app/transactions">
              <span class="icon"><i class="lni lni-wallet"></i></span>
              <span class="text">Transactions</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/app/budgets">
              <span class="icon"><i class="lni lni-coin"></i></span>
              <span class="text">Budgets</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/app/categories">
              <span class="icon"><i class="lni lni-tag"></i></span>
              <span class="text">Categories</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/app/debug">
              <span class="icon"><i class="lni lni-cog"></i></span>
              <span class="text">Debug</span>
            </router-link>
          </li>
   
          <!-- <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              class="collapsed"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_2"
              aria-controls="ddmenu_2"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="icon"></span>
              <span class="text">Pages</span>
            </a>
            <ul id="ddmenu_2" class="collapse dropdown-nav">
              <li><a href="settings.html"> Settings </a></li>
              <li><a href="blank-page.html"> Blank Page </a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="invoice.html">
              <span class="icon"></span>
              <span class="text">Invoice</span>
            </a>
          </li> -->
          <!-- <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              class="collapsed"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_3"
              aria-controls="ddmenu_3"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="icon"></span>
              <span class="text">Auth</span>
            </a>
            <ul id="ddmenu_3" class="collapse dropdown-nav">
              <li><router-link to="/signin"> Sign In </router-link></li>
              <li><router-link to="/signup"> Sign Up </router-link></li>
            </ul>
          </li> -->
          <span class="divider"><hr /></span>
        </ul>
      </nav>
    </aside>

    <!-- Main Wrapper -->
    <main class="main-wrapper">
      <!-- Header -->
      <header class="header">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-5 col-md-5 col-6">
              <div class="header-left d-flex align-items-center">
                <div class="menu-toggle-btn mr-15">
                  <button id="menu-toggle" class="main-btn primary-btn btn-hover">
                    <i class="lni lni-chevron-left me-2"></i> Menu
                  </button>
                </div>
                <div class="header-search d-none d-md-flex">
                  <form action="#">
                    <input type="text" placeholder="Search..." />
                    <button><i class="lni lni-search-alt"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-lg-7 col-md-7 col-6">
              <div class="header-right d-flex justify-content-end">
                <!-- Notification -->
                <div class="notification-box ml-15 d-none d-md-flex">
                  <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg width="22" height="22" fill="none"></svg>
                    <span></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a href="#0">
                        <div class="image"><img src="assets/images/lead/lead-6.png" alt="" /></div>
                        <div class="content">
                          <h6>John Doe <span class="text-regular">commented on a product.</span></h6>
                          <p>Lorem ipsum dolor sit amet...</p>
                          <span>10 mins ago</span>
                        </div>
                      </a>
                    </li>
                  </ul>
                </div>
                <!-- Message -->
                <div class="header-message-box ml-15 d-none d-md-flex">
                  <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a href="#0">
                        <div class="image"><img src="assets/images/lead/lead-5.png" alt="" /></div>
                        <div class="content">
                          <h6>Jacob Jones</h6>
                          <p>Hey! I came across your profile...</p>
                          <span>10 mins ago</span>
                        </div>
                      </a>
                    </li>
                  </ul>
                </div>
                <!-- Profile -->
                <div class="profile-box ml-15">
                  <button class="dropdown-toggle bg-transparent border-0" type="button" data-bs-toggle="dropdown">
                    <div class="profile-info">
                      <div class="image"><img src="assets/images/profile/profile-image.png" alt="" /></div>
                      <div>
                        <h6 class="fw-500">Adam Joe</h6>
                        <p>Admin</p>
                      </div>
                    </div>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <div class="author-info flex items-center !p-1">
                        <div class="image"><img src="assets/images/profile/profile-image.png" alt="image" /></div>
                        <div class="content">
                          <h4 class="text-sm">Adam Joe</h4>
                          <a href="#" class="text-xs text-muted">Email@gmail.com</a>
                        </div>
                      </div>
                    </li>
                    <li class="divider"></li>
                    <li><a href="#"><i class="lni lni-user"></i> View Profile</a></li>
                    <li><a href="#"><i class="lni lni-exit"></i> Sign Out</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
      <section class="tab-components">
        <div class="container-fluid">
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0">Dashboard</a>
                      </li>
                      <li class="breadcrumb-item"><a href="#0">Forms</a></li>
                      <li class="breadcrumb-item active" aria-current="page">
                        Form Elements
                      </li>
                    </ol>
                  </nav>
                </div>
              </div>
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
        </div>
        <!-- end container -->
      </section>
      <!-- Main Content -->
      <section class="page-content p-3">

        <router-view />
      </section>
    </main>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style scoped>
nav a {
  margin-right: 15px;
}
.page-content {
  padding-top: 20px;
}
</style>
