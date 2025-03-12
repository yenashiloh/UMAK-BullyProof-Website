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
                        <a href="{{route ('admin.list.list-perpetrators')}}">Complainees</a>
                    </li>
                    <li class="nav-item">
                        <a href="">Complainees</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                 
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">List of Complainant Report</h4>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                            </div>
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table id="basic-datatables" class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Report Number</th>
                                                <th>Date Filed</th>
                                                <th>Complainant's Name</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($formattedReports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ \Carbon\Carbon::parse($report['reportDate'])->setTimezone('Asia/Manila')->format('F d, Y g:i A') }}</td>

                                                <td>{{ $report['reporterFullName'] }}</td>
                                                <td>
                                                    @if($report['status'] == 'For Review')
                                                        <span class="badge badge-warning">For Review</span>
                                                    @elseif($report['status'] == 'In Progress')
                                                        <span class="badge badge-info">In Progress</span>
                                                    @elseif($report['status'] == 'Resolved')
                                                        <span class="badge badge-success">Resolved</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $report['status'] }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-button-action">
                                                        <a href="{{ route('admin.list.view-report', ['id' => $report['_id']]) }}" 
                                                           class="btn btn-link btn-primary" data-bs-toggle="tooltip"
                                                           title="View Report" data-original-title="View Report">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </div>
                                                    <a href="javascript:void(0)"
                                                    class="btn btn-link btn-secondary" data-bs-toggle="tooltip"
                                                    title="Print Report" onclick="printReportDirectly('{{ $report['_id'] }}')">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No reports found for this perpetrator</td>
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
        </div>
        <!-- End Custom template -->
    </div>
    <iframe id="printFrame" style="display:none;"></iframe>
    @include('partials.admin-footer')
    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });
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
