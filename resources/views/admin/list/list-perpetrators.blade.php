<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>List of Perpetrator</title>

    @include('partials.admin-link')
    <style>
        th{
            text-align: center;
        }
    </style>
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

    <!-- jQuery (required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Complainees</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                <li class="nav-item fw-bold">
                        <a href="#">Complainees</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">List of Complainees</h4>
                            <div class="ms-auto d-flex align-items-center">
                                {{-- <div class="dropdown">
                                    <a href="#" class="btn btn-primary btn-round dropdown-toggle" id="exportDropdown"
                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-file-export"></i> Export
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li>
                                            <a class="dropdown-item export-link no-loading" href="{{ route('export.csv') }}">
                                                <i class="fas fa-file-csv"></i> Export to CSV
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item export-link no-loading" href="{{ route('export.xlsx') }}">
                                                <i class="fas fa-file-excel"></i> Export to XLSX
                                            </a>
                                        </li>
                                    </ul>
                                </div> --}}
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Complainee's Name</th>
                                            <th>ID Number</th>
                                            <th>Role</th>
                                            <th>Violation Count</th>
                                            {{-- <th>View</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($filteredPerpetrators as $index => $perpetrator)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $perpetrator['name'] }}</td>
                                                <td>{{ $perpetrator['idNumber'] ? ucfirst($perpetrator['idNumber']) : 'Not identified' }}</td>

                                                <td>{{ $perpetrator['role'] }}</td>
                                                <td>{{ $perpetrator['count'] }}</td>
                                                {{-- <td class="text-center">
                                                    @if(!empty($perpetrator['idNumber']))
                                                        <a href="{{ route('admin.list.view-perpertrators', ['id' => $perpetrator['idNumber']]) }}"
                                                           class="btn btn-link btn-primary" data-bs-toggle="tooltip"
                                                           title="View" data-original-title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.list.view-perpertrators', ['id' => $perpetrator['reports'][0]]) }}"
                                                           class="btn btn-link btn-primary" data-bs-toggle="tooltip"
                                                           title="View" data-original-title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </td> --}}
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
