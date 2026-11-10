@if (session('toast_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ session('toast_success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
@endif

@if (session('toast_error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: '{{ session('toast_error') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
@endif

<script>
    // Loading alert
    function showLoading(message = 'Processing...') {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 3000,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Delete confirmation
    function confirmDelete(formId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading('Deleting...');
                document.getElementById(formId).submit();
            }
        });
    }

    // Loading state for forms
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form:not([data-no-loading])').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.hasAttribute('data-no-loading')) {
                    showLoading('Processing...');
                }
            });
        });

        // Loading state for links except certain ones
        document.querySelectorAll('a:not([href^="#"]):not([data-bs-toggle]):not([data-no-loading])').forEach(
            link => {
                link.addEventListener('click', () => {
                    if (!link.hasAttribute('data-no-loading')) {
                        showLoading('Loading...');
                    }
                });
            });
    });
</script>
