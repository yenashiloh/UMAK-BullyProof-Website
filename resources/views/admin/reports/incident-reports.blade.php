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
                        <a href="#">Export</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">List of Incident Reports</h4>
                            <div class="dropdown">
                                <a href="#" class="btn btn-primary btn-round dropdown-toggle" id="exportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-export"></i> Export
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                    <li>
                                        <a class="dropdown-item export-link no-loading" href="{{ route('reports.export.csv') }}">
                                            <i class="fas fa-file-csv"></i> Export to CSV
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item export-link no-loading" href="{{ route('reports.export.xlsx') }}">
                                            <i class="fas fa-file-excel"></i> Export to XLSX
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date and Time</th>
                                            <th>Complainant's Name</th>
                                            <th>Respondent's Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $index => $report)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($report['reportDate']->toDateTime())->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}</td>
                                            <td>{{ $report['victimName'] }}</td>
                                            <td>{{ $report['perpetratorName'] }}</td>
                                    
                                            <td>
                                                @if($report['status'] == 'For Review')
                                                    <span class="badge bg-primary ">{{ $report['status'] }}</span>
                                                @elseif($report['status'] == 'Under Investigation')
                                                    <span class="badge bg-warning text-dark">{{ $report['status'] }}</span>
                                                @elseif($report['status'] == 'Resolved')
                                                    <span class="badge bg-success">{{ $report['status'] }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $report['status'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-button-action d-grid gap-2">
                                                    <a href="{{ route('admin.reports.view', ['id' => $report['_id']]) }}" 
                                                        class="btn btn-sm btn-info" 
                                                        data-bs-toggle="tooltip" 
                                                        title="View Report" 
                                                        data-original-title="View">
                                                        View
                                                    </a>
                                                    
                                                    @if($report['status'] == 'For Review')
                                                    <form action="{{ route('admin.reports.changeStatus', ['id' => $report['_id']]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-warning w-100" data-bs-toggle="tooltip" title="Change Status" data-original-title="Change Status">
                                                            Under Investigation
                                                        </button>
                                                    </form>
                                                    @elseif($report['status'] == 'Under Investigation')
                                                    <form action="{{ route('admin.reports.changeStatus', ['id' => $report['_id']]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success w-100" data-bs-toggle="tooltip" title="Change Status" data-original-title="Change Status">
                                                            Resolve Incident
                                                        </button>
                                                    </form>
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

    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });
    </script>
