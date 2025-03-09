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
                                        <option value="Resolved">Resolved</option>
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
                                                        <span
                                                            class="badge status-badge bg-{{ $report['status'] == 'For Review'
                                                                ? 'primary'
                                                                : ($report['status'] == 'Under Investigation'
                                                                    ? 'warning text-white'
                                                                    : ($report['status'] == 'Resolved'
                                                                        ? 'success'
                                                                        : 'secondary')) }} d-flex justify-content-between align-items-center w-100"
                                                            style="min-width: 180px; cursor: {{ $report['status'] != 'Resolved' ? 'pointer' : 'default' }};"
                                                            @if ($report['status'] != 'Resolved') data-bs-toggle="dropdown" aria-expanded="false" @endif>
                                                            <span>{{ $report['status'] }}</span>
                                                            @if ($report['status'] != 'Resolved')
                                                                <i class="fas fa-chevron-down ms-auto"></i>
                                                            @endif
                                                        </span>
                                                        @if ($report['status'] != 'Resolved')
                                                            <ul class="dropdown-menu w-100">
                                                                @if ($report['status'] == 'For Review')
                                                                    <form
                                                                        action="{{ route('admin.reports.changeStatus', ['id' => $report['_id']]) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <li>
                                                                            <button class="dropdown-item" type="submit"
                                                                                name="status"
                                                                                value="Under Investigation">
                                                                                Mark as Under Investigation
                                                                            </button>
                                                                        </li>
                                                                    </form>
                                                                @elseif($report['status'] == 'Under Investigation')
                                                                    <form
                                                                        action="{{ route('admin.reports.changeStatus', ['id' => $report['_id']]) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <li>
                                                                            <button class="dropdown-item" type="submit"
                                                                                name="status" value="Resolved">
                                                                                Mark as Resolved
                                                                            </button>
                                                                        </li>
                                                                    </form>
                                                                @endif
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="form-button-action d-flex gap-2">
                                                        <a href="{{ route('admin.reports.view', ['id' => $report['_id']]) }}"
                                                            class="btn btn-link btn-secondary" data-bs-toggle="tooltip"
                                                            title="View Report">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                        class="btn btn-link btn-secondary" data-bs-toggle="tooltip"
                                                        title="Print Report" onclick="printReportDirectly('{{ $report['_id'] }}')">
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

                // Custom filtering function
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    if (selectedStatus === 'all') return true;

                    // Extract status text from the badge span
                    const statusCell = $(dataTable.cell(dataIndex, 4).node());
                    const statusText = statusCell.find('span').text().trim();

                    return statusText === selectedStatus;
                });

                // Redraw the table
                dataTable.draw();

                // Clear the custom filter
                $.fn.dataTable.ext.search.pop();
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
            loadingDiv.innerHTML = '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;"><span style="font-size: 18px;"><i class="fas fa-spinner fa-spin"></i> Preparing print...</span></div>';
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
                        }, 1000); 
                    };
                },
                error: function(xhr) {
                    console.error("Error loading print content:", xhr.responseText);
                    document.body.removeChild(loadingDiv);
                    
                    alert("There was an error preparing the print. Please try again.");
                }
            });
        }
    </script>
