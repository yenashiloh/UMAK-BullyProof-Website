<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <title>Appointment</title>
    @include('partials.admin-link')
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
                <h3 class="fw-bold mb-3">Appointment</h3>
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
                        <a href="{{ route('admin.appointment.appointment') }}">Appointment</a>
                    </li>
                </ul>
            </div>

            <script>
                var appointments = {!! json_encode($appointments) !!};
            </script>

            <div class="row">
                <div class="col-md-9 order-md-1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <h4 class="card-title m-0">Schedule Appointment</h4>
                            <button class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#newAppointmentModal">
                                <i class="fas fa-plus"></i> New Appointment
                            </button>
                        </div>
                        <div class="ui container p-3">
                            <div class="ui grid">
                                <div class="ui sixteen column">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 order-md-2">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="fw-bold">Status Badges</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="event-waiting-for-confirmation me-2"
                                        style="min-width: 20px; height: 20px; border-radius: 3px;"></div>
                                    <span>Waiting for Confirmation</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="event-approved me-2"
                                        style="min-width: 20px; height: 20px; border-radius: 3px;"></div>
                                    <span>Approved</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rescheduled me-2"
                                        style="min-width: 20px; height: 20px; border-radius: 3px;"></div>
                                    <span>Rescheduled</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="event-cancelled me-2"
                                        style="min-width: 20px; height: 20px; border-radius: 3px;"></div>
                                    <span>Cancelled</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="event-missed me-2"
                                        style="min-width: 20px; height: 20px; border-radius: 3px;"></div>
                                    <span>Missed</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="event-done me-2"
                                        style="min-width: 20px; height: 20px; border-radius: 3px;"></div>
                                    <span>Done</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="holiday me-2"
                                        style="min-width: 20px; height: 20px; border-radius: 3px; background-color: rgba(196, 4, 186, 0.1) !important;">
                                    </div>
                                    <span>Holiday</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Holiday List Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold">Upcoming Holidays</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled" id="holiday-list">
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Modal Add New Appointment-->
                <div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="newAppointmentModalLabel">New Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="max-height: 70vh; overflow-y: auto; overflow-x: hidden;">
                                <form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm" novalidate>
                                    @csrf
                                    
                                    <!-- Complainee Information Section -->
                                    <h6 class="fw-bold mb-3">Complainee Information</h6>
                                    <div class="row mb-3">
                                        <!-- Two columns for name and email -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="respondentName" class="form-label">Complainee Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="respondentName" name="respondent_name" placeholder="Enter Complainee Name" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="respondentEmail" class="form-label">Complainee Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="respondentEmail" placeholder="Enter Complainee Email" name="respondent_email" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                
                                        <!-- Full width for department email -->
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="complaineeDepartmentEmail" class="form-label">Complainee's Department Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="complaineeDepartmentEmail" placeholder="Enter Department Email" name="complainee_department_email" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                
                                    <!-- Complainant Information Section -->
                                    <h6 class="fw-bold mb-3">Complainant Information</h6>
                                    <div class="row mb-4">
                                        <!-- Two columns for name and email -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="complainantName" class="form-label">Complainant Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="complainantName" placeholder="Enter Complainant Name" name="complainant_name" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="complainantEmail" class="form-label">Complainant Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="complainantEmail" placeholder="Enter Complainant email" name="complainant_email" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                
                                        <!-- Full width for department email -->
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="complainantDepartmentEmail" class="form-label">Complainant's Department Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="complainantDepartmentEmail" placeholder="Enter Complainant's Department Email" name="complainant_department_email" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                
                                    <!-- Schedule Details Section -->
                                    <h6 class="fw-bold mb-3">Schedule Details</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="appointmentDate" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="appointmentDate" name="appointment_date" placeholder="Select date">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="appointmentStartTime" class="form-label">Start Time <span class="text-danger">*</span></label>
                                                <select class="form-control" id="appointmentStartTime" name="appointment_start_time">
                                                    <option value="">Select start time</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="appointmentEndTime" class="form-label">End Time <span class="text-danger">*</span></label>
                                                <select class="form-control" id="appointmentEndTime" name="appointment_end_time" disabled>
                                                    <option value="">Select end time</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
                                <button type="submit" form="appointmentForm" class="btn btn-secondary">Submit Appointment</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Appointment Details-->
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
                                    <span id="modalDescription"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 px-2">
                                    <span class="fw-bold">Email Address:</span>
                                    <span id="modalComplainantEmail"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 px-2">
                                    <span class="fw-bold">Department Email Address:</span>
                                    <span id="modalDepartmentComplainantEmail"></span>
                                </div>
                 
                                <hr class="my-4">
                 
                                <h6 class="fw-bold mb-4 mt-3">Complainee Information</h6>
                                <div class="d-flex justify-content-between mb-3 px-2">
                                    <span class="fw-bold">Full Name:</span>
                                    <span id="modalRespondent"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 px-2">
                                    <span class="fw-bold">Email Address:</span>
                                    <span id="modalRespondentEmail"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 px-2">
                                    <span class="fw-bold">Department Email Address:</span>
                                    <span id="modalDepartmentComplaineeEmail"></span>
                                </div>
                 
                                <hr class="my-4">
                 
                                <h6 class="fw-bold mb-4 mt-3">Schedule Details</h6>
                                <div class="d-flex justify-content-between mb-3 px-2">
                                    <span class="fw-bold">Status:</span>
                                    <span id="modalStatus" class="status-badge" style="text-transform: capitalize;"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 px-2">
                                    <span class="fw-bold">Date & Time:</span>
                                    <span id="modalTime"></span>
                                </div>
                            </div>
                            <div class="modal-footer py-3 px-4">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
        <!-- End Custom template -->
    </div>

    @include('partials.admin-footer')
    {{-- <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script> --}}
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="../../../../assets/js/appointment.js"></script>
    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });
        $(function() {
            $("#appointmentDate").datepicker({
                dateFormat: "mm/dd/yy" 
            });

            $("#appointmentDate").on('keydown', function(event) {
                event.preventDefault();
            });
        });
    </script>
