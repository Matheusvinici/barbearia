@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
