<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Create Account</title>

    @include('partials.admin-link')
</head>

<body>

    <div id="loading-overlay">
        <img id="loading-logo" src="{{ asset('assets/img/logo-4.png') }}" alt="Loading Logo">
        <div class="spinner"></div>
    </div>


    @include('partials.admin-sidebar')
    @include('partials.admin-header')
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- jQuery (required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Create Account</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>

                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item fw-bold">
                        <a href="#">Create Account</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Create Account</div>
                        </div>
                        <div class="card-body">
                            <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>
                            <form action="{{ route('admin.users.store') }}" method="POST" id="createAccountForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" required tabindex="1" />
                                            <div class="invalid-feedback" id="first_name_error"></div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" required tabindex="2" />
                                            <div class="invalid-feedback" id="last_name_error"></div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required tabindex="3" />
                                            <div class="invalid-feedback" id="email_error"></div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="contact_number">Contact Number</label>
                                            <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Enter Contact Number" required tabindex="4" />
                                            <div class="invalid-feedback" id="contact_number_error"></div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required tabindex="5" />
                                            <div class="invalid-feedback" id="password_error"></div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required tabindex="6" />
                                            <div class="invalid-feedback" id="password_confirmation_error"></div>
                                        </div>
                                    </div>
                            
                                    <div class="card-action">
                                        <button type="submit" class="btn btn-secondary" tabindex="7">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Custom template -->
    </div>

    @include('partials.admin-footer')
    <script src="../../../../assets/js/create-account.js"></script>
    <script>
        window.sessionData = {
            successMessage: @json(session('success')),
            errorMessage: @json(session('error'))
        };
    </script>
    
