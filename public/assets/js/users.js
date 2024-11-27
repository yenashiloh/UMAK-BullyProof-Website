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
            // Immediately reload the page
            sessionStorage.setItem('successMessage', "Status updated successfully!");
            location.reload();

            // Send the POST request in the background (async)
            fetch(`/admin/users/change-status/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    status: newStatus,
                }),
            }).catch((error) => {
                console.error('Error while updating status:', error);
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
        metaTag.content = document.querySelector('meta[name="csrf-token"]').content;
        document.head.appendChild(metaTag);
    }
});
