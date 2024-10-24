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

    <!-- jQuery (required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
                        <a href="#">Incidents Reports</a>
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
                                            <th>Victim's Name</th>
                                            {{-- <th>Grade Year/Level</th> --}}
                                            <th>Status</th>
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
                                            {{-- <td>{{ $report['gradeYearLevel'] }}</td> --}}
                                            <td>
                                                @if($report['status'] == 'To Review')
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
                                                <div class="form-button-action">
                                                    <a href="{{ route('admin.reports.view', ['id' => $report['_id']]) }}" 
                                                        class="btn btn-sm btn-info" 
                                                        data-bs-toggle="tooltip" 
                                                        title="View Report" 
                                                        data-original-title="View">
                                                        View
                                                    </a>
                                                </div>

                                                    @if($report['status'] == 'To Review')
                                                    <form action="{{ route('admin.reports.changeStatus', ['id' => $report['_id']]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-warning " data-bs-toggle="tooltip" title="Change Status" data-original-title="Change Status">
                                                            Under Investigation
                                                        </button>
                                                    </form>
                                                    @elseif($report['status'] == 'Under Investigation')
                                                    <form action="{{ route('admin.reports.changeStatus', ['id' => $report['_id']]) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Change Status" data-original-title="Change Status">
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
