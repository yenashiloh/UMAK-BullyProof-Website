<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Users</title>

    @include('partials.admin-link')
</head>

<body>

    <div id="loading-overlay">
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
                                                        <span class="badge {{ $user['status'] == 'Active Account' ? 'badge-success' : ($user['status'] == 'Disabled Account' ? 'badge-danger' : 'badge-secondary') }} dropdown-toggle" id="statusDropdown{{ $user['_id'] }}" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                                                            {{ $user['status'] }}
                                                            @if ($user['status'] == 'Active Account' || $user['status'] == 'Disabled Account')
                                                                <i class="fas fa-chevron-down ms-2"></i>
                                                            @endif
                                                        </span>
                                                        @if ($user['status'] == 'Active Account' || $user['status'] == 'Disabled Account')
                                                            <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $user['_id'] }}">
                                                                @if ($user['status'] == 'Active Account')
                                                                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="changeStatus('{{ $user['_id'] }}', 'Disabled Account')">Disable Account</a></li>
                                                                @elseif ($user['status'] == 'Disabled Account')
                                                                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="changeStatus('{{ $user['_id'] }}', 'Active Account')">Activate Account</a></li>
                                                                @endif
                                                            </ul>
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
    <!-- Toastify CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">

    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.js"></script>

    @include('partials.admin-footer')
    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });

        function changeStatus(userId, newStatus) {
    // SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to change the status to ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // Send a POST request to update the status
            fetch(`/admin/users/change-status/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Create and show custom toast
                    createCustomToast("Status updated successfully!", "success");

                    // Update the entire row to reflect real-time changes
                    const userRow = document.querySelector(`tr:has(#statusDropdown${userId})`);
                    if (userRow) {
                        // Update status badge
                        const statusBadge = userRow.querySelector(`#statusDropdown${userId}`);
                        statusBadge.className = `badge 
                            ${newStatus === 'Active Account' ? 'badge-success' : 
                              newStatus === 'Disabled Account' ? 'badge-danger' : 'badge-secondary'} 
                            dropdown-toggle-no-caret 
                            ${['Active Account', 'Disabled Account'].includes(newStatus) ? 'dropdown-toggle' : ''}`;
                        
                        statusBadge.innerHTML = `${newStatus} 
                            ${['Active Account', 'Disabled Account'].includes(newStatus) ? 
                              '<i class="fas fa-chevron-down ms-2"></i>' : ''}`;

                        // Update dropdown menu if applicable
                        const dropdownMenu = userRow.querySelector('.dropdown-menu');
                        if (dropdownMenu) {
                            dropdownMenu.innerHTML = newStatus === 'Active Account' ?
                                `<li><a class="dropdown-item" href="javascript:void(0);" 
                                    onclick="changeStatus('${userId}', 'Disabled Account')">
                                    Disable Account</a></li>` :
                                `<li><a class="dropdown-item" href="javascript:void(0);" 
                                    onclick="changeStatus('${userId}', 'Active Account')">
                                    Activate Account</a></li>`;
                        }
                    }
                } else {
                    createCustomToast("Failed to update status", "error");
                }
            })
            .catch(error => {
                createCustomToast("Error updating status", "error");
                console.error('Error:', error);
            });
        }
    });
}

// Custom toast notification function
function createCustomToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        console.error('Toast container not found');
        return;
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);
    
    // Initialize and show the toast using Bootstrap
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove the toast after it closes
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Ensure CSRF token is set in meta tag
document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = '{{ csrf_token() }}';
        document.head.appendChild(metaTag);
    }
});
    </script>
