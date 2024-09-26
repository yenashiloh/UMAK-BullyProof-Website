document.getElementById('logoutButton').addEventListener('click', function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout'
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const logoutUrl = this.getAttribute('data-logout-url');

            fetch(logoutUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.href = 'login';
                } else {
                    throw new Error('Logout was not successful');
                }
            })
            .catch(error => {
                console.error('Error during logout:', error);
                Swal.fire('Error', 'An error occurred during logout. Please try again.', 'error');
            });
        }
    });
});
