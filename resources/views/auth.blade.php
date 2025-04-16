    @include('templates._head')
    <main>
      <section class="tab-components">
        <div class="container-fluid">
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="title">
                  <h2>Famsheets</h2>
                </div>
                <div class="title-logo">
                  <a href="/">
                    <img src="assets/images/logo/logo.svg" />
                  </a>
                </div>
              </div>
              <!-- end col -->
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
          <div id="auth">
            @vite(['resources/css/app.css', 'resources/js/auth.js'])
          </div>
        </div>
        <!-- end container -->
      </section>
  </main>
@include('templates._foot') 
