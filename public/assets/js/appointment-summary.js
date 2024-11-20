//date tables
$(document).ready(function() {
    $('#basic-datatables').DataTable().destroy(); 
    $('#basic-datatables').DataTable(); 
});

//show success message toast
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

//change the status of appointment 
function changeStatus(appointmentId, newStatus) {
    fetch('/appointments/change-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            appointment_id: appointmentId,
            new_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status updated successfully!', 'success');
            updateStatusBadge(appointmentId, newStatus);
        } else {
            showToast('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        showToast('An error occurred while updating the status.', 'danger');
    });
}

function updateStatusBadge(appointmentId, newStatus) {
    const dropdownContainer = document.querySelector(`#statusDropdown${appointmentId}`).closest('.dropdown');
    const badge = dropdownContainer.querySelector('.badge');
    const dropdownMenu = dropdownContainer.querySelector('.dropdown-menu');

    badge.textContent = newStatus;
    badge.classList.remove(
        'event-waiting-for-confirmation', 
        'event-approved', 
        'event-cancelled', 
        'event-missed', 
        'event-done', 
        'rescheduled', 
        'dropdown-toggle'
    );

    let badgeClass = '';
    switch (newStatus) {
        case 'Waiting for Confirmation':
            badgeClass = 'event-waiting-for-confirmation';
            break;
        case 'Approved':
            badgeClass = 'event-approved';
            break;
        case 'Cancelled':
            badgeClass = 'event-cancelled';
            break;
        case 'Missed':
            badgeClass = 'event-missed';
            break;
        case 'Done':
            badgeClass = 'event-done';
            break;
        case 'Rescheduled':
            badgeClass = 'rescheduled';
            break;
        default:
            badgeClass = 'bg-secondary'; 
            break;
    }
    
    badge.classList.add(badgeClass);

    if (newStatus === 'Waiting for Confirmation' || newStatus === 'Approved') {
        badge.classList.add('dropdown-toggle');
        badge.setAttribute('data-bs-toggle', 'dropdown');

        if (newStatus === 'Waiting for Confirmation') {
            dropdownMenu.innerHTML = `
                <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointmentId}', 'Approved')">Mark as Approved</a></li>
                <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointmentId}', 'Cancelled')">Mark as Cancelled</a></li>
            `;
        } else if (newStatus === 'Approved') {
            dropdownMenu.innerHTML = `
                <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointmentId}', 'Cancelled')">Mark as Cancelled</a></li>
                <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointmentId}', 'Missed')">Mark as Missed</a></li>
                <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointmentId}', 'Done')">Mark as Done</a></li>
            `;
        }

        let chevronIcon = badge.querySelector('.fas.fa-chevron-down');
        if (!chevronIcon) {
            chevronIcon = document.createElement('i');
            chevronIcon.classList.add('fas', 'fa-chevron-down', 'ms-2');
            badge.appendChild(chevronIcon);
        }
    } else {
        badge.classList.remove('dropdown-toggle');
        badge.removeAttribute('data-bs-toggle');
        if (dropdownMenu) {
            dropdownMenu.remove();
        }

        const chevronIcon = badge.querySelector('.fas.fa-chevron-down');
        if (chevronIcon) {
            chevronIcon.remove();
        }
    }
}

//filter status
document.addEventListener('DOMContentLoaded', function () {
    const dataTable = $('#basic-datatables').DataTable();

    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: true,
        locale: {
            format: 'MM/DD/YYYY',
            cancelLabel: 'Clear'
        },
        startDate: moment(), 
        endDate: moment(),   
        showDropdowns: true,
        ranges: {           
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        const startDate = picker.startDate.format('YYYY-MM-DD');
        const endDate = picker.endDate.format('YYYY-MM-DD');
        console.log("Date Range selected: " + startDate + ' to ' + endDate);
        const selectedStatus = $('#statusFilter').val();
        fetchAppointments(startDate, endDate, selectedStatus);
    });

    $('#statusFilter').on('change', function() {
        const dateRange = $('input[name="daterange"]').val().split(' - ');
        if (dateRange.length === 2) {
            const startDate = moment(dateRange[0], 'MM/DD/YYYY').format('YYYY-MM-DD');
            const endDate = moment(dateRange[1], 'MM/DD/YYYY').format('YYYY-MM-DD');
            fetchAppointments(startDate, endDate, $(this).val());
        }
    });

    function fetchAppointments(startDate, endDate, status = '') {
        fetch('/appointments/filter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                startDate: startDate,
                endDate: endDate
            })
        })
        .then(response => response.json())
        .then(data => {
            dataTable.clear();
            const filteredAppointments = data.appointments.filter(appointment => {
                const appointmentDate = moment(appointment.start);
                return appointmentDate.isBetween(startDate, endDate, 'day', '[]');
            });
    
            filteredAppointments.forEach((appointment, index) => {
                const appointmentDate = moment(appointment.start);
                let badgeHtml = '';
                
                if (appointment.status === 'Waiting for Confirmation' || appointment.status === 'Approved') {
                    badgeHtml = `
                        <div class="dropdown">
                            <span id="statusDropdown${appointment.id}" class="badge ${getBadgeClass(appointment.status)}" data-bs-toggle="dropdown" style="cursor: pointer;">
                                ${appointment.status}
                                <i class="fas fa-chevron-down ms-2"></i>
                            </span>
                            <ul class="dropdown-menu">
                    `;
                    
                    if (appointment.status === 'Waiting for Confirmation') {
                        badgeHtml += `
                            <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointment.id}', 'Approved')">Mark as Approved</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointment.id}', 'Cancelled')">Mark as Cancelled</a></li>
                        `;
                    } else if (appointment.status === 'Approved') {
                        badgeHtml += `
                            <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointment.id}', 'Cancelled')">Mark as Cancelled</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointment.id}', 'Missed')">Mark as Missed</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeStatus('${appointment.id}', 'Done')">Mark as Done</a></li>
                        `;
                    }

                    badgeHtml += `</ul></div>`;
                } else {
                    badgeHtml = `<span class="badge ${getBadgeClass(appointment.status)}">${appointment.status}</span>`;
                }

                dataTable.row.add([
                    appointmentDate.format('MMMM Do YYYY'),
                    appointment.client_name,
                    appointment.course,
                    appointment.instructor_name,
                    badgeHtml
                ]).draw();
            });
        })
        .catch(error => console.error('Error fetching appointments:', error));
    }

    function getBadgeClass(status) {
        switch (status) {
            case 'Waiting for Confirmation':
                return 'event-waiting-for-confirmation';
            case 'Approved':
                return 'event-approved';
            case 'Cancelled':
                return 'event-cancelled';
            case 'Missed':
                return 'event-missed';
            case 'Done':
                return 'event-done';
            case 'Rescheduled':
                return 'rescheduled';
            default:
                return 'bg-secondary'; // Default class for unrecognized statuses
        }
    }
});
