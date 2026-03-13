{{-- Flash Messages --}}
@if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
<div id="flashMessages" class="mb-3">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-left:4px solid #16a34a;border-radius:10px;">
        <i class="fas fa-check-circle fa-lg" style="color:#16a34a;flex-shrink:0;"></i>
        <div class="flex-grow-1">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-left:4px solid #ef4444;border-radius:10px;">
        <i class="fas fa-exclamation-circle fa-lg" style="color:#ef4444;flex-shrink:0;"></i>
        <div class="flex-grow-1">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-left:4px solid #f59e0b;border-radius:10px;">
        <i class="fas fa-exclamation-triangle fa-lg" style="color:#f59e0b;flex-shrink:0;"></i>
        <div class="flex-grow-1">{{ session('warning') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert" style="border-left:4px solid #3b82f6;border-radius:10px;">
        <i class="fas fa-info-circle fa-lg" style="color:#3b82f6;flex-shrink:0;"></i>
        <div class="flex-grow-1">{{ session('info') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left:4px solid #ef4444;border-radius:10px;">
        <div class="d-flex align-items-start gap-2">
            <i class="fas fa-exclamation-circle fa-lg mt-1" style="color:#ef4444;flex-shrink:0;"></i>
            <div class="flex-grow-1">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li style="font-size:13px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

</div>

<script>
// Auto-dismiss success and info alerts after 4 seconds
document.addEventListener('DOMContentLoaded', function () {
    const autoDismiss = ['alert-success', 'alert-info'];
    autoDismiss.forEach(function (cls) {
        document.querySelectorAll('.' + cls + '[role="alert"]').forEach(function (el) {
            setTimeout(function () {
                if (el && typeof bootstrap !== 'undefined') {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                    if (bsAlert) bsAlert.close();
                }
            }, 4000);
        });
    });
});
</script>
@endif
