<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Incidents Report</title>

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

    <style>
        .dropdown-menu {
            min-width: 100%;
        }

        .status-badge {
            padding: 6px 10px;
            font-size: 13px;
        }
    </style>
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Incidents Report</h3>
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
                        <a href="#">Export</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">List of Incident Reports</h4>
                        </div>
                        <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">

                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="statusFilter" class="form-label fw-bold">
                                        <h6 class="fw-bold">Filter Status:</h6>
                                    </label>
                                    <select id="statusFilter" class="form-select">
                                        <option value="all">All</option>
                                        <option value="For Review">For Review</option>
                                        <option value="Under Investigation">Under Investigation</option>
                                        <option value="Awaiting Response">Awaiting Response</option>
                                        <option value="Resolved">Resolved</option>
                                        <option value="Dismissed">Dismissed</option>
                                        <option value="Reopened">Reopened</option>

                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date Filed</th>
                                            <th>Complainant's Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportsTableBody">
                                        @forelse ($reports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $report['formatted_date'] }}</td>
                                                <td>{{ $report['complainant_name'] }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <span class="badge status-badge bg-{{ $report['status_class'] }} d-flex justify-content-between align-items-center w-100"
                                                            style="min-width: 180px; cursor: {{ in_array($report['status'], ['Resolved']) ? 'default' : 'pointer' }};"
                                                            {{ !in_array($report['status'], ['Resolved']) ? 'data-bs-toggle="dropdown" aria-expanded="false"' : '' }}>
                                                            <span>{{ $report['status'] }}</span>
                                                            @if (!in_array($report['status'], ['Resolved']))
                                                                <i class="fas fa-chevron-down ms-auto"></i>
                                                            @endif
                                                        </span>
                                                        @if (!in_array($report['status'], ['Resolved']))
                                                            <ul class="dropdown-menu w-100">
                                                                <form action="{{ route('admin.reports.changeStatus', $report['_id']) }}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    @switch($report['status'])
                                                                        @case('For Review')
                                                                            <li><button class="dropdown-item under-investigation-btn" type="submit" name="status" value="Under Investigation" data-report-id="{{ $report['_id'] }}">Under Investigation</button></li>
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Dismissed">Dismiss Report</button></li>
                                                                            @break
                                                                        @case('Under Investigation')
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Awaiting Response">Awaiting Response</button></li>
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Resolved">Resolved</button></li>
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Dismissed">Dismiss Report</button></li>
                                                                            @break
                                                                        @case('Awaiting Response')
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Resolved">Resolved</button></li>
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Dismissed">Dismiss Report</button></li>
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Withdrawn">Withdraw Report</button></li>
                                                                            @break
                                                                        @case('Reopened')
                                                                            <li><button class="dropdown-item under-investigation-btn" type="submit" name="status" value="Under Investigation" data-report-id="{{ $report['_id'] }}">Under Investigation</button></li>
                                                                            @break
                                                                        @case('Dismissed')
                                                                        @case('Withdrawn')
                                                                            <li><button class="dropdown-item" type="submit" name="status" value="Reopened">Reopen Report</button></li>
                                                                            @break
                                                                    @endswitch
                                                                </form>
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="{{ route('admin.reports.view-other-report', $report['_id']) }}" class="btn btn-link btn-primary btn-lg">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No reports found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Custom template -->

        <!-- Modal component for creating an appointment -->
        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="appointmentModalLabel"><strong>Schedule Investigation
                                Appointment</strong></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="appointmentForm">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <small>
                                    <p><strong>Note:</strong> The following details will be automatically included:</p>
                                    <ul>
                                        <li>Complainee's name and email </li>
                                        <li>Complainant's name and email</li>
                                        <li>Department emails for both parties</li>
                                    </ul>
                                    <p>Emails will be sent to all relevant parties automatically.</p>
                                </small>
                            </div>

                            <input type="hidden" id="report_id" name="report_id">

                            <br>

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">Appointment Date</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                                    required>
                                <div class="invalid-feedback" id="appointment_date_error"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_start_time" class="form-label">Start Time</label>
                                        <select class="form-control" id="appointment_start_time"
                                            name="appointment_start_time" required></select>
                                        <div class="invalid-feedback" id="appointment_start_time_error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_end_time" class="form-label">End Time</label>
                                        <select class="form-control" id="appointment_end_time"
                                            name="appointment_end_time" required></select>
                                        <div class="invalid-feedback" id="appointment_end_time_error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitAppointment">
                                Schedule Appointment
                                <span class="spinner-border spinner-border-sm d-none" id="appointmentSpinner"
                                    role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <iframe id="printFrame" style="display:none;"></iframe>
    @include('partials.admin-footer')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#basic-datatables').DataTable({
                "order": [[1, "desc"]], // Sort by date filed by default (second column)
                "pageLength": 10,
                "language": {
                    "emptyTable": "No reports found"
                },
                "columnDefs": [
                    {
                        "targets": 0, // The index column
                        "orderable": false // Disable sorting for the index column
                    },
                    {
                        "targets": 1, // Ensure the date column is properly formatted
                        "type": "date"
                    }
                ]
            });
        });
    </script>
