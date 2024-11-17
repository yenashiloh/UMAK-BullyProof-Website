<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' />

    <title>Users</title>

    @include('partials.admin-link')

    <style>
        /* .fc-content {
            background-color: rgb(202, 202, 255);
        } */

        .fc-day-number {
            color: black;
        }

        .fc-event {
            border: none !important;
            background: none !important;
        }

        .fc-content {
            padding: 2px 5px;
        }

        .fc-time {
            font-weight: bold;
        }

        .event-waiting-for-confirmation {
            background-color: #c7edff !important;
            border: 1px solid #e0e0e0 !important;
            color: white !important;
        }

        .event-approved {
            background-color: #d3ebb9 !important;
            border: 1px solid #e0e0e0 !important;
            color: white !important;
        }

        .event-cancelled {
            background-color: #FFCDD2 !important;
            border: 1px solid #e0e0e0 !important;
            color: white !important;
        }

        .event-missed {
            background-color: #ffec9a !important;
            border: 1px solid #e0e0e0 !important;
            color: white !important;
        }

        .event-done {
            background-color: #72f791 !important;
            border: 1px solid #e0e0e0 !important;
            color: white !important;
        }
    </style>

</head>

<body>

    <div id="loading-overlay">
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
                        <a href="{{ route('admin.users.users') }}">Appointment</a>
                    </li>
                </ul>
            </div>

            <script>
                var appointments = {!! json_encode($appointments) !!};
            </script>

            <div class="row p-4">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <h4 class="card-title m-0">Schedule Appointment</h4>
                            <button class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#newAppointmentModal">
                                <i class="fas fa-plus"></i> New Appointment
                            </button>
                        </div>

                        <div class="ui container p-3">
                            <div class="ui container">
                                <div class="ui grid">
                                    <div class="ui sixteen column">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body p-3">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Modal Add New Appointment-->
            <div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="newAppointmentModalLabel">New Appointment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; overflow-x: hidden;">

                            <form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm"
                                novalidate>
                                @csrf
                                <!-- Respondent Name -->
                                <div class="mb-3">
                                    <label for="respondentName" class="form-label fw-bold">Respondent Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="respondentName"
                                        name="respondent_name" placeholder="Enter respondent name" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Respondent Email -->
                                <div class="mb-3">
                                    <label for="respondentEmail" class="form-label fw-bold">Respondent Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="respondentEmail"
                                        placeholder="Enter respondent email" name="respondent_email" required
                                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Complainant Name -->
                                <div class="mb-3">
                                    <label for="complainantName" class="form-label fw-bold">Complainant Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="complainantName"
                                        placeholder="Enter complainant name" name="complainant_name" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Complainant Email -->
                                <div class="mb-3">
                                    <label for="complainantEmail" class="form-label fw-bold">Complainant Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="complainantEmail"
                                        placeholder="Enter complainant email" name="complainant_email" required
                                        pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Row for Appointment Date and Time -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="appointmentDate" class="form-label fw-bold">Appointment Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="appointmentDate"
                                            name="appointment_date" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="appointmentTime" class="form-label fw-bold">Appointment
                                            Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="appointmentTime"
                                            name="appointment_time" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-dark"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="appointmentForm" class="btn btn-secondary">Save
                                Appointment</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Appointment Details-->
            <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="appointmentModalLabel">Appointment Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right fw-bold">Complainant:</label>
                                <div class="col-md-8 mt-2">
                                    <div id="modalDescription"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right fw-bold">Complainant Email:</label>
                                <div class="col-md-8 mt-2">
                                    <div id="modalComplainantEmail"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right fw-bold">Respondent:</label>
                                <div class="col-md-8 mt-2">
                                    <div id="modalRespondent"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right fw-bold">Respondent Email:</label>
                                <div class="col-md-8 mt-2">
                                    <div id="modalRespondentEmail"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right fw-bold">Status:</label>
                                <div class="col-md-8 mt-2">
                                    <div id="modalStatus" class="status-badge" style="text-transform: capitalize;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-right fw-bold">Date & Time:</label>
                                <div class="col-md-8 mt-2">
                                    <div id="modalTime"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <script src="../../../../assets/js/appointment.js"></script>
    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });
    </script>
