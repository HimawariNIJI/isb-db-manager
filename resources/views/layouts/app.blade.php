<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'ISB DB Manager')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global confirm modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmModalMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmModalCancel" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmModalConfirm" class="btn btn-danger">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const confirmModalEl = document.getElementById('confirmModal');
            const confirmModal = new bootstrap.Modal(confirmModalEl);
            const messageEl = document.getElementById('confirmModalMessage');
            const confirmBtn = document.getElementById('confirmModalConfirm');

            let pendingForm = null;

            function showConfirm(message, form) {
                messageEl.textContent = message;
                pendingForm = form;
                confirmModal.show();
            }

            confirmBtn.addEventListener('click', function () {
                if (!pendingForm) return;
                // remove any temporary handler and submit
                pendingForm.removeEventListener('submit', submitHandler);
                confirmModal.hide();
                pendingForm.submit();
                pendingForm = null;
            });

            function submitHandler(e) {
                e.preventDefault();
                const form = e.currentTarget;
                const msg = form.getAttribute('data-confirm') || 'Apakah Anda yakin?';
                showConfirm(msg, form);
            }

            // Attach to all forms that use data-confirm attribute
            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', submitHandler);
            });
        });
    </script>

</body>
</html>