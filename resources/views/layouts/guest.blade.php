<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title') · StudentManagement</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <aside class="auth-story">
        <div class="brand"><span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span><span>StudentManagement<small>QUẢN LÝ ĐÀO TẠO</small></span></div>
        <div class="auth-intro"><div class="badge border border-light border-opacity-25 mb-4 px-3 py-2">KHÔNG GIAN QUẢN LÝ HỌC TẬP</div><h2>Kết nối lớp học.<br>Đồng hành tri thức.</h2><p class="mt-4">Quản lý lớp học, sinh viên và hoạt động đào tạo trong một không gian thống nhất.</p><div class="d-flex gap-4 mt-4 small"><span><i class="bi bi-shield-check me-2"></i>Bảo mật tài khoản</span><span><i class="bi bi-grid me-2"></i>Quản lý tập trung</span></div></div>
        <small>© {{ now()->year }} StudentManagement</small>
    </aside>
    <main class="auth-form-panel"><div class="auth-form-inner">
        <div class="stat-icon mb-4"><i class="bi bi-box-arrow-in-right"></i></div>
        <h1 class="h2">@yield('title')</h1><p class="text-muted mb-4">@yield('subtitle')</p>
        @if ($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
        @yield('content')
    </div></main>
</div>
</body>
</html>
