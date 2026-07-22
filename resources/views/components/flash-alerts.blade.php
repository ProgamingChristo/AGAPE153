@php
    $alert = null;

    if (session('success') || session('status')) {
        $alert = [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => session('success') ?: session('status'),
            'messages' => [],
        ];
    } elseif (session('error')) {
        $alert = [
            'icon' => 'error',
            'title' => 'Gagal',
            'text' => session('error'),
            'messages' => [],
        ];
    } elseif ($errors->any()) {
        $alert = [
            'icon' => 'error',
            'title' => 'Periksa kembali input',
            'text' => 'Ada data yang belum valid.',
            'messages' => $errors->all(),
        ];
    }
@endphp

@if ($alert)
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const payload = @json($alert);

            if (!window.Swal) {
                return;
            }

            const escapeHtml = (value) => String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const html = payload.messages.length
                ? `<div class="text-left"><p>${escapeHtml(payload.text)}</p><ul class="mt-3 list-disc pl-5">${payload.messages.map((message) => `<li>${escapeHtml(message)}</li>`).join('')}</ul></div>`
                : escapeHtml(payload.text);

            window.Swal.fire({
                icon: payload.icon,
                title: payload.title,
                html,
                confirmButtonText: payload.icon === 'success' ? 'OK' : 'Perbaiki',
                confirmButtonColor: payload.icon === 'success' ? '#0f766e' : '#dc2626',
                timer: payload.icon === 'success' ? 2600 : undefined,
                timerProgressBar: payload.icon === 'success',
            });
        });
    </script>
@endif
