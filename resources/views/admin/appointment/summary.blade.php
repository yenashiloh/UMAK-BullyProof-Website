<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Appointment Summary</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



    <!-- jQuery (required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        .dropdown-menu {
            min-width: 100%;
        }

        .dropdown-toggle-no-caret::after {
            display: none !important;
        }
    </style>
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Appointment Summary</h3>
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
                        <a href="#">Appointment Summary</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>
                        <div class="card-body">        
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="statusFilter" class="form-label fw-bold"><h6 class="fw-bold">Filter Status:</h6></label>
                                    <select id="statusFilter" class="form-select">
                                        <option value="">All</option>
                                        <option value="Waiting for Confirmation">Waiting for Confirmation</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rescheduled">Rescheduled</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Missed">Missed</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </div>
                            
                                <div class="col-md-6">
                                    <label for="daterange" class="form-label fw-bold"><h6 class="fw-bold">Filter Appointment Date:</h6></label>
                                    <input type="text" name="daterange" id="daterange" class="form-control" />
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Appointment Date</th>
                                            <th>Time</th>
                                            <th>Complainee Name</th>                                         
                                            <th>Complainant Name</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appointmentsTableBody">
                                        @foreach ($appointments as $appointment)
                                            <tr data-status="{{ $appointment['status'] }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($appointment['start'])->format('F j, Y') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($appointment['start'])->format('g:i A') }} -  {{ \Carbon\Carbon::parse($appointment['appointment_end_time'])->format('g:i A') }}
                                                </td>
                                                <td>{{ $appointment['title'] }}</td>
                                               
                                                <td>{{ $appointment['description'] }}</td>
                                               
                                                <td>
                                                    <div class="dropdown">
                                                        <span class="badge
                                                            {{ $appointment['status'] == 'Approved' ? 'event-approved' : 
                                                               ($appointment['status'] == 'Cancelled' ? 'event-cancelled' : 
                                                               ($appointment['status'] == 'Missed' ? 'event-missed' : 
                                                               ($appointment['status'] == 'Done' ? 'event-done' : 
                                                               ($appointment['status'] == 'Rescheduled' ? 'rescheduled' : 
                                                               ($appointment['status'] == 'Waiting for Confirmation' ? 'event-waiting-for-confirmation' : 'bg-secondary'))))) }} 
                                                            dropdown-toggle-no-caret
                                                            {{ $appointment['status'] == 'Waiting for Confirmation' || $appointment['status'] == 'Approved' ? 'dropdown-toggle' : '' }}"
                                                            id="statusDropdown{{ $appointment['id'] }}"
                                                            {{ $appointment['status'] == 'Waiting for Confirmation' || $appointment['status'] == 'Approved' ? 'data-bs-toggle=dropdown aria-expanded=false style=cursor:pointer;' : '' }}>
                                                            {{ $appointment['status'] }}
                                                            @if ($appointment['status'] == 'Waiting for Confirmation' || $appointment['status'] == 'Approved')
                                                                <i class="fas fa-chevron-down ms-2"></i>
                                                            @endif
                                                        </span>
                                                        @if ($appointment['status'] == 'Waiting for Confirmation' || $appointment['status'] == 'Approved')
                                                            <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $appointment['id'] }}">
                                                                @if ($appointment['status'] == 'Waiting for Confirmation')
                                                                    <li><a class="dropdown-item" href="#" onclick="changeStatus('{{ $appointment['id'] }}', 'Approved')">Mark as Approved</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="changeStatus('{{ $appointment['id'] }}', 'Rescheduled')">Mark as Rescheduled</a></li>
                                                                @elseif ($appointment['status'] == 'Approved')
                                                                   
                                                                    <li><a class="dropdown-item" href="#" onclick="changeStatus('{{ $appointment['id'] }}', 'Missed')">Mark as Missed</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="changeStatus('{{ $appointment['id'] }}', 'Rescheduled')">Mark as Rescheduled</a></li>
                                                                    <li><a class="dropdown-item" href="#" onclick="changeStatus('{{ $appointment['id'] }}', 'Done')">Mark as Done</a></li>
                                                                @endif
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    <div class="form-button-action d-grid gap-2">
                                                        <a href="#" 
                                                           class="btn btn-link btn-secondary" 
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#appointmentModal"
                                                           onclick="viewAppointment({{ json_encode($appointment) }})"
                                                           title="View Appointment">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Details Modal -->
        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header py-3 px-4">
                        <h5 class="modal-title fw-bold" id="appointmentModalLabel">Appointment Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 75vh; overflow-y: auto; overflow-x: hidden; padding: 20px;">
                        <h6 class="fw-bold mb-4 mt-2">Complainant Information</h6>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Full Name:</span>
                            <span id="modal-complainant-name"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Email Address:</span>
                            <span id="modal-complainant-email"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Department Email Address:</span>
                            <span id="modal-complainant-dept"></span>
                        </div>
        
                        <hr class="my-4">
        
                        <h6 class="fw-bold mb-4 mt-3">Complainee Information</h6>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Full Name:</span>
                            <span id="modal-complainee-name"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Email Address:</span>
                            <span id="modal-complainee-email"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Department Email Address:</span>
                            <span id="modal-complainee-dept"></span>
                        </div>
        
                        <hr class="my-4">
        
                        <h6 class="fw-bold mb-4 mt-3">Schedule Details</h6>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Status:</span>
                            <span id="modal-status" class="status-badge" style="text-transform: capitalize;"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 px-2">
                            <span class="fw-bold">Date & Time:</span>
                            <span id="modal-time"></span>
                        </div>
                    </div>
                    <div class="modal-footer py-3 px-4">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Custom template -->
    </div>
    
    

    @include('partials.admin-footer')
    <script src="../../../../assets/js/appointment-summary.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
    function viewAppointment(appointment) {
        const appointmentDate = new Date(appointment.start).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const startTime = new Date(appointment.start).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        const endTime = new Date(appointment.appointment_end_time).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        document.getElementById('modal-complainant-name').textContent = appointment.description;
        document.getElementById('modal-complainant-email').textContent = appointment.complainant_email;
        document.getElementById('modal-complainant-dept').textContent = appointment.complainant_department_email;
        
        document.getElementById('modal-complainee-name').textContent = appointment.title;
        document.getElementById('modal-complainee-email').textContent = appointment.respondent_email;
        document.getElementById('modal-complainee-dept').textContent = appointment.complainee_department_email;
        
        document.getElementById('modal-status').textContent = appointment.status;
        document.getElementById('modal-time').textContent = `${appointmentDate} ${startTime} - ${endTime}`;
    }
    </script>