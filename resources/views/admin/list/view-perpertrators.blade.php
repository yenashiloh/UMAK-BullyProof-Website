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
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                 
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">List of Incident Reports</h4>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                            </div>
                            <h5>All Reports</h5>
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
                                        @foreach ($reports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ \Carbon\Carbon::parse($report['reportDate']->toDateTime())->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}
                                                </td>
                                                <td>{{ ucwords(strtolower($report['reporterFullName'])) }}</td>                                             
                                                <td>
                                                    <div class="badge status-badge bg-{{ $report['status'] == 'For Review'
                                                        ? 'primary'
                                                        : ($report['status'] == 'Under Investigation'
                                                            ? 'warning text-white'
                                                            : ($report['status'] == 'Resolved'
                                                                ? 'success'
                                                                : 'secondary')) }} d-flex justify-content-center align-items-center w-100"
                                                        style="min-width: 130px; cursor: default;">
                                                        <span>{{ $report['status'] }}</span>
                                                    </div>
                                                </td>                                                
                                                <td>
                                                    <div class="form-button-action d-flex">
                                                        <a href="{{ route('admin.list.view-report', ['id' => $report['_id']]) }}"
                                                            class="btn btn-link btn-secondary" data-bs-toggle="tooltip"
                                                            title="View Report">
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
        <!-- End Custom template -->
    </div>

    @include('partials.admin-footer')
    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });
    </script>
