$(document).ready(function () {
    $("#basic-datatables").DataTable({});

    // Check if a success message exists in sessionStorage
    const successMessage = sessionStorage.getItem('successMessage');
    if (successMessage) {
        // Show the success toast
        createCustomToast(successMessage, "success");
        // Clear the message from sessionStorage
        sessionStorage.removeItem('successMessage');
    }
});

function changeStatus(userId, newStatus) {
    if (newStatus === 'Disabled Account') {
        Swal.fire({
            title: 'Disable Account',
            html: `
                <div class="form-group">
                    <label for="disable-reason" class="form-label">Reason for disabling account:</label>
                    <textarea id="disable-reason" class="form-control" rows="6" placeholder="Please provide a reason"></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Disable Account',
            cancelButtonText: 'Cancel',
            didOpen: () => {
                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.innerHTML = `<span class="button-text">Disable Account</span>`;
            },
            preConfirm: () => {
                const reason = document.getElementById('disable-reason').value.trim();
                if (!reason) {
                    Swal.showValidationMessage('Please provide a reason for disabling the account');
                    return false;
                }

                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    <span class="button-text">Disabling...</span>
                `;

                return fetch(`/admin/users/change-status/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        reason: reason,
                        sendEmail: true
                    }),
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .catch((error) => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `<span class="button-text">Disable Account</span>`;
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Account Disabled!',
                    text: 'The account has been successfully disabled and an email has been sent.',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => location.reload());
            }
        });

    } else {
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to change the status to ${newStatus}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            didOpen: () => {
                Swal.getConfirmButton().innerHTML = `<span class="button-text">Yes</span>`;
            },
            preConfirm: () => {
                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    <span class="button-text">Processing...</span>
                `;

                return fetch(`/admin/users/change-status/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        sendEmail: newStatus === 'Active Account'
                    }),
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .catch((error) => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `<span class="button-text">Yes</span>`;
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated!',
                    text: 'The user status has been updated successfully.',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => location.reload());
            }
        });
    }
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
        metaTag.content = document.querySelector('meta[name="csrf-token"]').content;
        document.head.appendChild(metaTag);
    }
});

/**
 * Reason for disabled accounts
 */
