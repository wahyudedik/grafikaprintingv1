@if (session('toast_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ session('toast_success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
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
            timerProgressBar: true
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
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            showLoading('Processing...');
        });
    });

    // Loading state for links
    document.querySelectorAll('a:not([href^="#"])').forEach(link => {
        link.addEventListener('click', () => {
            showLoading('Loading...');
        });
    });
</script>
