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
                            {{-- <div class="dropdown">
                                <a href="#" class="btn btn-primary btn-round dropdown-toggle" id="exportDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-export"></i> Export
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                    <li>
                                        <a class="dropdown-item export-link no-loading"
                                            href="{{ route('reports.export.csv') }}">
                                            <i class="fas fa-file-csv"></i> Export to CSV
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item export-link no-loading"
                                            href="{{ route('reports.export.xlsx') }}">
                                            <i class="fas fa-file-excel"></i> Export to XLSX
                                        </a>
                                    </li>
                                </ul>
                            </div> --}}
                        </div>
                        <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">

                        </div>

                        <div class="card-body">
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
                                            <th>Complainee's Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportsTableBody">
                                        @foreach ($reports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ \Carbon\Carbon::parse($report['reportDate']->toDateTime())->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}
                                                </td>
                                                <td>{{ ucwords(strtolower($report['reporterFullName'])) }}</td>
                                                <td>{{ ucwords(strtolower($report['perpetratorName'])) }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        @php
                                                            $statusClass = match ($report['status']) {
                                                                'For Review' => 'primary',
                                                                'Under Investigation' => 'warning text-white',
                                                                'Resolved' => 'success',
                                                                'Dismissed' => 'danger',
                                                                'Reopened' => 'dark',
                                                                'Awaiting Response' => 'secondary',
                                                                'Withdrawn' => 'danger',
                                                                default => 'secondary',
                                                            };
                                                        @endphp

                                                        <span
                                                            class="badge status-badge bg-{{ $statusClass }} d-flex justify-content-between align-items-center w-100"
                                                            style="min-width: 180px; cursor: {{ in_array($report['status'], ['Resolved']) ? 'default' : 'pointer' }};"
                                                            @if (!in_array($report['status'], ['Resolved'])) data-bs-toggle="dropdown" aria-expanded="false" @endif>
                                                            <span>{{ $report['status'] }}</span>
                                                            @if (!in_array($report['status'], ['Resolved']))
                                                                <i class="fas fa-chevron-down ms-auto"></i>
                                                            @endif
                                                        </span>

                                                        @if (!in_array($report['status'], ['Resolved']))
                                                            <ul class="dropdown-menu w-100">
                                                                <form
                                                                    action="{{ route('admin.reports.changeStatus', ['id' => $report['_id']]) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')

                                                                    @if ($report['status'] == 'For Review')
                                                                        <li><button
                                                                                class="dropdown-item under-investigation-btn"
                                                                                type="submit" name="status"
                                                                                value="Under Investigation"
                                                                                data-report-id="{{ $report['_id'] }}">Under
                                                                                Investigation</button></li>

                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status" value="Dismissed">Dismiss
                                                                                Report</button></li>
                                                                    @elseif ($report['status'] == 'Under Investigation')
                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status"
                                                                                value="Awaiting Response">Awaiting
                                                                                Response</button></li>
                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status"
                                                                                value="Resolved">Resolved</button></li>
                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status" value="Dismissed">Dismiss
                                                                                Report</button></li>
                                                                    @elseif ($report['status'] == 'Awaiting Response')
                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status"
                                                                                value="Resolved">Resolved</button></li>
                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status" value="Dismissed">Dismiss
                                                                                Report</button></li>
                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status"
                                                                                value="Withdrawn">Withdraw
                                                                                Report</button></li>
                                                                    @elseif ($report['status'] == 'Reopened')
                                                                        <li><button
                                                                                class="dropdown-item under-investigation-btn"
                                                                                type="submit" name="status"
                                                                                value="Under Investigation"
                                                                                data-report-id="{{ $report['_id'] }}">Under
                                                                                Investigation</button></li>
                                                                    @elseif (in_array($report['status'], ['Dismissed', 'Withdrawn']))
                                                                        <li><button class="dropdown-item" type="submit"
                                                                                name="status" value="Reopened">Reopen
                                                                                Report</button></li>
                                                                    @endif
                                                                </form>
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="form-button-action d-flex gap-2">
                                                        <a href="{{ route('admin.reports.view', ['id' => $report['_id']]) }}"
                                                            class="btn btn-link btn-secondary"
                                                            data-bs-toggle="tooltip" title="View Report">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                            class="btn btn-link btn-secondary"
                                                            data-bs-toggle="tooltip" title="Print Report"
                                                            onclick="printReportDirectly('{{ $report['_id'] }}')">
                                                            <i class="fas fa-print"></i>
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
        <!-- End Custom template -->

        <!-- Modal component for creating an appointment -->
        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="appointmentModalLabel"><strong>Schedule Investigation
                                Appointment</strong></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
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
                                <input type="date" class="form-control" id="appointment_date"
                                    name="appointment_date" required>
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
            let dataTable = $('#basic-datatables').DataTable({
                "order": [
                    [1, "desc"]
                ], // Sort by date filed by default (second column)
                "pageLength": 10,
                "language": {
                    "emptyTable": "No reports found"
                },
                "columnDefs": [{
                        "targets": 0, // The index column (assuming it's the first column)
                        "orderable": false // Disable sorting for the index column
                    },
                    {
                        "targets": 1, // Ensure the date column is properly formatted
                        "type": "date"
                    }
                ]
            });

            // Add event listener to update row numbering
            dataTable.on('order.dt search.dt draw.dt', function() {
                dataTable
                    .column(0, {
                        search: 'applied',
                        order: 'applied'
                    })
                    .nodes()
                    .each(function(cell, i) {
                        cell.innerHTML = i + 1; // Set row numbering
                    });
            }).draw();

            // Status filter change event
            $('#statusFilter').on('change', function() {
                const selectedStatus = $(this).val();

                // Clear any existing custom filter
                $.fn.dataTable.ext.search.pop();

                if (selectedStatus !== 'all') {
                    // Custom filtering function
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        // Get the actual DOM element for the status cell
                        const statusCell = $(dataTable.cell(dataIndex, 4).node());

                        // Find the status text which is in the first span element within the badge
                        const statusText = statusCell.find('.status-badge > span:first').text()
                            .trim();

                        return statusText === selectedStatus;
                    });
                }

                // Redraw the table
                dataTable.draw();
            });

            // Initially trigger the filter if a value is selected
            if ($('#statusFilter').val() !== 'all') {
                $('#statusFilter').trigger('change');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";
            const successType = "{{ session('toastType', 'success') }}";
            const errorType = "{{ session('toastType', 'danger') }}";

            if (successMessage) {
                showToast(successMessage, successType);
            }
            if (errorMessage) {
                showToast(errorMessage, errorType);
            }
        });

        function showToast(message, type) {
            const toastElement = document.createElement('div');
            toastElement.classList.add('toast', 'align-items-center', 'text-white', 'bg-' + type, 'border-0');
            toastElement.setAttribute('role', 'alert');
            toastElement.setAttribute('aria-live', 'assertive');
            toastElement.setAttribute('aria-atomic', 'true');
            toastElement.innerHTML = ` 
                <div class="d-flex"> 
                    <div class="toast-body"> 
                        ${message} 
                    </div> 
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button> 
                </div> 
            `;
            const toastContainer = document.getElementById('toastContainer');
            toastContainer.appendChild(toastElement);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        // Print report directly
        function printReportDirectly(id) {
            var loadingDiv = document.createElement('div');
            loadingDiv.id = 'printLoadingIndicator';
            loadingDiv.innerHTML =
                '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;"><span style="font-size: 18px;"><i class="fas fa-spinner fa-spin"></i> Preparing print...</span></div>';
            document.body.appendChild(loadingDiv);

            var iframe = document.getElementById('printFrame');

            $.ajax({
                url: "{{ route('admin.reports.get-print-content') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    iframe.srcdoc = response;

                    iframe.onload = function() {
                        iframe.contentWindow.print();

                        setTimeout(function() {
                            document.body.removeChild(loadingDiv);
                        }, 1000); // Remove the loading indicator after 1 second
                    };
                },
                error: function(xhr) {
                    console.error("Error loading print content:", xhr.responseText);
                    document.body.removeChild(loadingDiv);

                    alert("There was an error preparing the print. Please try again.");
                }
            });
        }

        //Appointment Modal

        // Generate time options hourly (8AM to 3PM)
        function generateStartTimeOptions() {
            const times = [];
            for (let hour = 8; hour <= 15; hour++) { // 8 AM to 3 PM full hour
                ['00', '30'].forEach(minute => {
                    let displayHour = hour > 12 ? hour - 12 : hour;
                    let ampm = hour >= 12 ? 'PM' : 'AM';
                    let formatted = `${displayHour}:${minute} ${ampm}`;
                    let value = `${String(hour).padStart(2, '0')}:${minute}`;
                    times.push({
                        value,
                        formatted
                    });
                });
            }
            // Add 4:00 PM separately
            times.push({
                value: "16:00",
                formatted: "4:00 PM"
            });
            return times;
        }

        // Generate end time options based on selected start time
        function generateEndTimeOptions(selectedStartTime) {
            const allTimes = generateStartTimeOptions();
            const selectedIndex = allTimes.findIndex(t => t.value === selectedStartTime);
            return allTimes.slice(selectedIndex + 1);
        }

        // Populate the start time dropdown
        function populateStartTimes() {
            const startSelect = document.getElementById('appointment_start_time');
            startSelect.innerHTML = '<option value="">Select start time</option>';

            const startTimes = generateStartTimeOptions();
            startTimes.forEach(time => {
                const option = new Option(time.formatted, time.value);
                startSelect.add(option);
            });

            const endSelect = document.getElementById('appointment_end_time');
            endSelect.innerHTML = '<option value="">Select end time</option>';
            endSelect.disabled = true;
        }

        // Update end time dropdown when start time is selected
        function updateEndTimes() {
            const startSelect = document.getElementById('appointment_start_time');
            const endSelect = document.getElementById('appointment_end_time');
            const selectedStartTime = startSelect.value;

            if (selectedStartTime) {
                const endTimes = generateEndTimeOptions(selectedStartTime);
                endSelect.innerHTML = '<option value="">Select end time</option>';

                endTimes.forEach(time => {
                    const option = new Option(time.formatted, time.value);
                    endSelect.add(option);
                });

                endSelect.disabled = false;
            } else {
                endSelect.innerHTML = '<option value="">Select end time</option>';
                endSelect.disabled = true;
            }
        }

        // Show the appointment modal
        function showAppointmentModal(reportId) {
            document.getElementById('report_id').value = reportId;

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointment_date').min = today;

            populateStartTimes();
            clearAppointmentErrors();

            const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            modal.show();
        }

        // Clear errors
        function clearAppointmentErrors() {
            const errorFields = [
                'appointment_date_error',
                'appointment_start_time_error',
                'appointment_end_time_error'
            ];
            errorFields.forEach(field => {
                document.getElementById(field).textContent = '';
                document.getElementById(field.replace('_error', '')).classList.remove('is-invalid');
            });
        }

        // Block weekends
        document.addEventListener('DOMContentLoaded', function() {
            const appointmentDateInput = document.getElementById('appointment_date');

            appointmentDateInput.addEventListener('input', function() {
                const selectedDate = new Date(this.value);
                const day = selectedDate.getDay();
                if (day === 0 || day === 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date',
                        text: 'Weekends are not allowed. Please select a weekday.',
                        confirmButtonColor: '#3085d6'
                    });
                    this.value = '';
                }
            });

            document.getElementById('appointment_start_time').addEventListener('change', updateEndTimes);

            const underInvestigationButtons = document.querySelectorAll('.under-investigation-btn');
            underInvestigationButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const reportId = this.getAttribute('data-report-id');
                    showAppointmentModal(reportId);
                });
            });

            populateStartTimes();
        });

        // Form submit
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const spinner = document.getElementById('appointmentSpinner');
            const submitButton = document.getElementById('submitAppointment');
            spinner.classList.remove('d-none');
            submitButton.disabled = true;

            const formData = {
                report_id: document.getElementById('report_id').value,
                appointment_date: document.getElementById('appointment_date').value,
                appointment_start_time: document.getElementById('appointment_start_time').value,
                appointment_end_time: document.getElementById('appointment_end_time').value
            };

            const baseUrl = window.location.origin;
            fetch(`${baseUrl}/appointments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    spinner.classList.add('d-none');
                    submitButton.disabled = false;

                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('appointmentModal')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const errorField = document.getElementById(`${key}_error`);
                                if (errorField) {
                                    errorField.textContent = data.errors[key][0];
                                    document.getElementById(key).classList.add('is-invalid');
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'An error occurred. Please try again.',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    }
                })
                .catch(error => {
                    spinner.classList.add('d-none');
                    submitButton.disabled = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                    console.error('Error:', error);
                });
        });
    </script>
