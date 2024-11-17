<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Incidents Report</title>

    @include('partials.admin-link')
</head>

<body>

    <div id="loading-overlay">
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
                                            <th>Respondent Name</th>
                                            <th>Respondent Email</th>
                                            <th>Complainant Name</th>
                                            <th>Complainant Email</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appointmentsTableBody">
                                        @foreach ($appointments as $appointment)
                                            <tr data-status="{{ $appointment['status'] }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($appointment['start'])->format('F j, Y') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($appointment['start'])->format('g:i A') }}
                                                </td>
                                                <td>{{ $appointment['title'] }}</td>
                                                <td>{{ $appointment['respondent_email'] }}</td>
                                                <td>{{ $appointment['description'] }}</td>
                                                <td>{{ $appointment['complainant_email'] }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <span
                                                            class="badge {{ $appointment['status'] == 'Approved' ? 'bg-success' : ($appointment['status'] == 'Cancelled' ? 'bg-danger' : ($appointment['status'] == 'Missed' ? 'bg-warning' : ($appointment['status'] == 'Done' ? 'bg-success' : 'bg-secondary'))) }} 
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
                                                            <ul class="dropdown-menu"
                                                                aria-labelledby="statusDropdown{{ $appointment['id'] }}">
                                                                @if ($appointment['status'] == 'Waiting for Confirmation')
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="changeStatus('{{ $appointment['id'] }}', 'Approved')">Mark
                                                                            as Approved</a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="changeStatus('{{ $appointment['id'] }}', 'Cancelled')">Mark
                                                                            as Cancelled</a></li>
                                                                @else
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="changeStatus('{{ $appointment['id'] }}', 'Cancelled')">Mark
                                                                            as Cancelled</a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="changeStatus('{{ $appointment['id'] }}', 'Missed')">Mark
                                                                            as Missed</a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="changeStatus('{{ $appointment['id'] }}', 'Done')">Mark
                                                                            as Done</a></li>
                                                                @endif
                                                            </ul>
                                                        @endif
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
        <!-- End Custom template -->
    </div>

    @include('partials.admin-footer')
    <script src="../../../../assets/js/appointment-summary.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>