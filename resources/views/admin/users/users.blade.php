<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Users</title>

    @include('partials.admin-link')
</head>

<body>

    <div id="loading-overlay">
        <img id="loading-logo" src="{{ asset('assets/img/logo-4.png') }}" alt="Loading Logo">
        <div class="spinner"></div>
    </div>
    
    @include('partials.admin-sidebar')
    @include('partials.admin-header')

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Users</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.users.users') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.users') }}">Users</a>
                    </li>
                </ul>
            </div>
            <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">List of Users</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Roles</th>
                                            <th>Status</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ isset($user['fullname']) ? ucwords(strtolower($user['fullname'])) : '' }}
                                                </td>
                                                <td>{{ $user['email'] ?? '' }}</td>
                                                <td>{{ $user['contact'] ?? '' }}</td>
                                                <td>{{ $user['type'] ?? '' }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        @if(session('admin_role') === 'superadmin')
                                                            <span
                                                                class="badge {{ $user['status'] == 'Active Account' ? 'badge-success' : ($user['status'] == 'Disabled Account' ? 'badge-danger' : 'badge-secondary') }}"
                                                                id="statusDropdown{{ $user['_id'] }}"
                                                                data-bs-toggle="dropdown" 
                                                                aria-expanded="false"
                                                                style="cursor: pointer; display: inline-flex; align-items: center;"
                                                            >
                                                                {{ $user['status'] }}
                                                                @if ($user['status'] == 'Active Account' || $user['status'] == 'Disabled Account')
                                                                    <i class="fas fa-chevron-down ms-2"></i>
                                                                @endif
                                                            </span>
                                                            @if ($user['status'] == 'Active Account' || $user['status'] == 'Disabled Account')
                                                                <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $user['_id'] }}">
                                                                    @if ($user['status'] == 'Active Account')
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="javascript:void(0);"
                                                                                onclick="changeStatus('{{ $user['_id'] }}', 'Disabled Account')"
                                                                            >
                                                                                Mark as Disabled Account
                                                                            </a>
                                                                        </li>
                                                                    @elseif ($user['status'] == 'Disabled Account')
                                                                        <li>
                                                                            <a class="dropdown-item"
                                                                                href="javascript:void(0);"
                                                                                onclick="changeStatus('{{ $user['_id'] }}', 'Active Account')"
                                                                            >
                                                                                Mark as Active Account
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                </ul>
                                                            @endif
                                                        @else
                                                            {{-- For discipline users, show badge only --}}
                                                            <span class="badge {{ $user['status'] == 'Active Account' ? 'badge-success' : ($user['status'] == 'Disabled Account' ? 'badge-danger' : 'badge-secondary') }}">
                                                                {{ $user['status'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                {{-- <td>
                                                    <div class="form-button-action d-grid gap-2">
                                                        <a href="{{ route('users.all-reports', ['userId' => $user->_id]) }}"
                                                            class="btn btn-link btn-secondary" 
                                                            data-bs-toggle="tooltip"
                                                            title="View Reports About User">
                                                             <i class="fas fa-eye"></i>
                                                         </a>
                                                    </div>
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
    
    <!-- Toastify CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">

    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.js"></script>
    <script src="../../../../assets/js/users.js"></script>


    @include('partials.admin-footer')
    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });
    </script>
