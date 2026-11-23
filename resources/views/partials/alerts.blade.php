@if (session('success'))
    <div class="alert alert-success" role="status">
        <span>✔</span><span>{{ session('success') }}</span>
        <button type="button" class="alert-close" aria-label="Kapat">&times;</button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error" role="alert">
        <span>✖</span><span>{{ session('error') }}</span>
        <button type="button" class="alert-close" aria-label="Kapat">&times;</button>
    </div>
@endif
