@if ($errors->any())
    <!-- Danger Toast -->
    <div class="bs-toast toast toast-ex animate__animated my-2 fade bg-danger animate__bounceInDown" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="toast-header">
            <i class='bx bx-bell me-2'></i>
            <div class="me-auto fw-medium">System Message</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        @foreach ($errors->all() as $error)
            <div class="toast-body">
                {{ $error }}
            </div>
        @endforeach
    </div>
@endif

@if (session('error'))
    <!-- Danger Toast -->
    <div class="bs-toast toast toast-ex animate__animated my-2 fade bg-danger animate__bounceInDown" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="toast-header">
            <i class='bx bx-bell me-2'></i>
            <div class="me-auto fw-medium">System Message</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('error') }}
        </div>
    </div>
@endif

@if (session('warning'))
    <!-- Warning Toast -->
    <div class="bs-toast toast toast-ex animate__animated my-2 fade bg-warning animate__bounceInDown" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="toast-header">
            <i class='bx bx-bell me-2'></i>
            <div class="me-auto fw-medium">System Message</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('warning') }}
        </div>
    </div>
@endif

@if (session('success'))
    <!-- Success Toast -->
    <div class="bs-toast toast toast-ex animate__animated my-2 fade bg-success animate__bounceInDown" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="toast-header">
            <i class='bx bx-bell me-2'></i>
            <div class="me-auto fw-medium">System Message</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session('info'))
    <!-- Info Toast -->
    <div class="bs-toast toast toast-ex animate__animated my-2 fade bg-info animate__bounceInDown" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="toast-header">
            <i class='bx bx-bell me-2'></i>
            <div class="me-auto fw-medium">System Message</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('info') }}
        </div>
    </div>
@endif
