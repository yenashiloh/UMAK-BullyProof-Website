<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Users</title>

    @include('partials.admin-link')
    <!-- Toastify CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <style>
        .ck-editor__editable {
            min-height: 300px;
        }
    </style>
</head>

<body>

    <div id="loading-overlay">
        <img id="loading-logo" src="{{ asset('assets/img/logo-4.png') }}" alt="Loading Logo">
        <div class="spinner"></div>
    </div>
    
    

    @include('partials.admin-sidebar')
    @include('partials.admin-header')

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Add Email Content</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.email.email-management') }}" class="fw-bold">Email</a>
                    </li>
                </ul>
            </div>
            <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
                        @if (session('toast'))
                            <script>
                                sessionStorage.setItem('successMessage', "{{ session('toast') }}");
                            </script>
                        @endif
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-line nav-color-secondary" id="line-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="line-home-tab" data-bs-toggle="pill"
                                        href="#line-home" role="tab" aria-controls="pills-home"
                                        aria-selected="true">Complainants</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="line-profile-tab" data-bs-toggle="pill" href="#line-profile"
                                        role="tab" aria-controls="pills-profile"
                                        aria-selected="false">Complainee</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="line-contact-tab" data-bs-toggle="pill" href="#line-contact"
                                        role="tab" aria-controls="pills-contact" aria-selected="false">Cancelled
                                        Appointment</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="line-reschedule-tab" data-bs-toggle="pill"
                                        href="#line-reschedule" role="tab" aria-controls="pills-reschedule"
                                        aria-selected="false">Reschedule
                                        Appointment</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3 mb-3" id="line-tabContent">
                                <form action="{{ route('storeEmailContent') }}" method="POST">
                                    @csrf
                                    <div class="tab-content mt-3 mb-3" id="line-tabContent">
                                        <!-- Complainants Tab -->
                                        <div class="tab-pane fade show active" id="line-home" role="tabpanel"
                                            aria-labelledby="line-home-tab">
                                            <div class="form-group">
                                                <!-- Textarea for CKEditor -->
                                                <textarea id="complainantEmailContent" name="complainant_email_content" class="form-control ms-3">{{ $complainantEmailContent }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Complainee Tab -->
                                        <div class="tab-pane fade" id="line-profile" role="tabpanel"
                                            aria-labelledby="line-profile-tab">
                                            <div class="form-group">
                                                <!-- Textarea for CKEditor -->
                                                <textarea id="complaineeEmailContent" name="complainee_email_content" class="form-control ms-3">{{ $complaineeEmailContent }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Cancelled Tab -->
                                        <div class="tab-pane fade" id="line-contact" role="tabpanel"
                                            aria-labelledby="line-contact-tab">
                                            <div class="form-group">
                                                <!-- Textarea for CKEditor -->
                                                <textarea id="cancelledEmailContent" name="cancelled_email_content" class="form-control ms-3">{{ $cancelledEmailContent }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Reschedule Appointment Tab -->
                                        <div class="tab-pane fade" id="line-reschedule" role="tabpanel"
                                            aria-labelledby="line-reschedule-tab">
                                            <div class="form-group">
                                                <!-- Textarea for CKEditor -->
                                                <textarea id="rescheduleEmailContent" name="reschedule_email_content" class="form-control ms-3">{{ $rescheduleEmailContent }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-secondary mt-3 ms-2">Save Content</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- End Custom template -->
    </div>

    @include('partials.admin-footer').
    <script>
        //text editor
        ClassicEditor
            .create(document.querySelector('#complainantEmailContent'))
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#complaineeEmailContent'))
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#cancelledEmailContent'))
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#rescheduleEmailContent'))
            .catch(error => {
                console.error(error);
            });
            $(document).ready(function() {
    const successMessage = sessionStorage.getItem('successMessage');
    if (successMessage) {
        createCustomToast(successMessage, "success");
        sessionStorage.removeItem('successMessage');  // Clear after showing
    }
});

// Function to show success message toast
function createCustomToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        console.error('Toast container not found');
        return;
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        delay: 5000
    });
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}


document.addEventListener("DOMContentLoaded", function () {
    const lineTab = document.getElementById('line-tab');
    const activeTab = localStorage.getItem('activeTab'); // Retrieve the active tab from local storage
    
    if (activeTab) {
        // Activate the tab from local storage
        const triggerEl = document.querySelector(`a[href="${activeTab}"]`);
        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    } else {
        // Default to the first tab if no tab is stored
        const firstTab = lineTab.querySelector('a[data-bs-toggle="pill"]');
        if (firstTab) {
            const tab = new bootstrap.Tab(firstTab);
            tab.show();
        }
    }

    // Store the active tab on click
    const tabs = lineTab.querySelectorAll('a[data-bs-toggle="pill"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const activeTabHref = e.target.getAttribute('href');
            localStorage.setItem('activeTab', activeTabHref); // Save the active tab in local storage
        });
    });
});
    </script>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastify JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>
