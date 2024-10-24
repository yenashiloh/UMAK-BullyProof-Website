<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/img/logo-3.png') }}" type="image/x-icon">
    <title>Admin Login</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../assets-admin/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets-admin/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets-admin/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets-admin/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets-admin/css/style.css">
    <!-- End layout styles -->

  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left p-5">
                <div class="brand-logo">
   
                </div>
                <h4>Hello, Admin!</h4>
                <h6 class="font-weight-light mb-4">Sign in to continue</h6>
                <!-- Error messages container -->
                  @if ($errors->any())
                  <div class="" style="color: red; margin-bottom: 10px; font-size: 13px;">
                     
                          @foreach ($errors->all() as $error)
                              {{ $error }}
                          @endforeach
                  </div>
                  @endif
                <form method="POST" action="{{ route('admin.login.submit') }}">
                  @csrf
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" value="" required>
                  </div>
                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
                  </div>
                  <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <script src="../../assets-admin/vendors/js/vendor.bundle.base.js"></script>
    <script src="../../assets-admin/js/off-canvas.js"></script>
    <script src="../../assets-admin/js/misc.js"></script>
    <script src="../../assets-admin/js/settings.js"></script>
    <script src="../../assets-admin/js/todolist.js"></script>
    <script src="../../assets-admin/js/jquery.cookie.js"></script>
    <!-- endinject -->
  </body>
</html>