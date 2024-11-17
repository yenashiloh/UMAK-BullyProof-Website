<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>List of Perpetrator</title>

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
                <h3 class="fw-bold mb-3">Respondents</h3>
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
                        <a href="#">Respondents</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Respondent's Name</th>
                                            <th>Grade/Year Level & Section</th>
                                            <th>ID Number</th>
                                            <th>Remarks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $report->perpetratorName ?? 'N/A' }}</td>
                                                <td>{{ $report->perpetratorGradeYearLevel ?? 'N/A' }}</td>
                                                <td>{{ $report->idNumber ?? 'N/A' }}</td> 
                                                <td>{{ $report->offenseCounts ?? 'N/A' }}</td> 
                                                <td class="d-flex">
                                                    <a href="{{ route('admin.list.view-perpertrators', ['id' => $report->_id]) }}"
                                                       class="btn btn-link btn-primary me-2" data-bs-toggle="tooltip"
                                                       title="View" data-original-title="View">
                                                       <i class="fa fa-eye"></i>
                                                    </a>
                                                
                                                    <a href="#" class="btn btn-link btn-primary" data-bs-toggle="tooltip"
                                                       title="Edit" data-original-title="Edit">
                                                       <i class="fa fa-edit"></i>
                                                    </a>
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
