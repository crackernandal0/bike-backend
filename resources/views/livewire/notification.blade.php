<div>
    <div>
        @if (session('error') || session('success'))
            @php
                if (session('error')) {
                    $class = 'bg-danger';
                    $type = 'Error';
                    $message = session('error');
                } else {
                    $class = 'bg-success';
                    $type = 'Success';
                    $message = session('success');
                }
            @endphp
            <div id="toast" class="bs-toast toast fade show {{ $class }} position-fixed"
                style="top: 2%;right:2%;" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-semibold">{{ $type }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ $message }}
                </div>
            </div>

            @push('scripts')
                <script>
                    const toastLiveExample = document.getElementById('toast')

                    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
                    toastBootstrap.show()
                </script>
            @endpush
        @endif
    </div>

</div>
