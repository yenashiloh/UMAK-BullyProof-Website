<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Profile</title>

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

    <!-- jQuery (required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Incident Reports</h3>
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
                        <a href="#">Incident Reports</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">List of Incident Reports</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date and Time</th>
                                            <th>Reported By</th>
                                            <th>Victim Name</th>
                                            <th>Grade Year/Level</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $index => $report)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($report['reportDate']->toDateTime())->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}</td>
                                            <td>{{ $report['reporterFullName'] }}</td>
                                            <td>{{ $report['victimName'] }}</td>
                                            <td>{{ $report['gradeYearLevel'] }}</td>
                                            <td>
                                                <div class="form-button-action">
                                                    <div class="form-button-action">
                                                        <a href="{{ route('admin.reports.view', ['id' => $report['_id']]) }}" 
                                                            class="btn btn-link btn-primary" 
                                                            data-bs-toggle="tooltip" 
                                                            title="View Report" 
                                                            data-original-title="View">
                                                             <i class="fa fa-eye"></i>
                                                         </a>
                                                    </div>
                                                    <button type="button" data-bs-toggle="tooltip"
                                                        title="Change Status" class="btn btn-link btn-primary "
                                                        data-original-title="Change Status">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
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
