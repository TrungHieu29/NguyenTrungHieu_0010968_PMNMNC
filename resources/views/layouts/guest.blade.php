<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · StudentManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{min-height:100vh;background:linear-gradient(135deg,#10375b,#3c7ca8);display:grid;place-items:center}.auth-card{width:min(100%,440px);border:0;border-radius:1.25rem;box-shadow:0 18px 60px #0a244055}</style>
</head>
<body><div class="card auth-card m-3"><div class="card-body p-4 p-md-5"><div class="text-center mb-4"><div class="fs-2">🎓</div><h1 class="h4 fw-bold">StudentManagement</h1><p class="text-muted mb-0">@yield('subtitle')</p></div>
@if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')</div></div></body></html>
