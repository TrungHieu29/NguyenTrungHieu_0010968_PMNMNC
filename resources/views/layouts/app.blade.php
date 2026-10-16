<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tổng quan') · StudentManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
@php
    $navigation = \App\Models\Menu::where('trang_thai', true)->orderBy('thu_tu')->orderBy('id')->get()->filter(fn ($item) => $item->isVisibleTo(auth()->user()));
    $icons = ['/dashboard' => 'grid-1x2', '/lop-hocs' => 'collection', '/sinh-viens' => 'mortarboard', '/menus' => 'layout-sidebar', '/users' => 'people', '/profile' => 'person-circle', '/api-playground' => 'braces'];
@endphp
<div class="app-shell">
    <aside class="app-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Điều hướng chính">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span><span>StudentManagement<small>QUẢN LÝ ĐÀO TẠO</small></span></a>
            <button class="btn-close btn-close-white mobile-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Đóng điều hướng"></button>
        </div>
        <div class="offcanvas-body">
            <div class="nav-caption">Không gian quản lý</div>
            <nav class="nav flex-column">
                @foreach ($navigation->where('vi_tri', 'sidebar') as $item)
                    <a class="nav-link {{ request()->is(ltrim($item->url, '/'), ltrim($item->url, '/').'/*') ? 'active' : '' }}" href="{{ $item->url }}"><i class="bi bi-{{ $icons[$item->url] ?? 'link-45deg' }}" aria-hidden="true"></i>{{ $item->ten_hien_thi }}</a>
                @endforeach
            </nav>
            <div class="sidebar-foot mt-auto"><div class="text-white fw-semibold mb-1"><i class="bi bi-shield-check me-2"></i>Phiên làm việc an toàn</div>{{ auth()->user()->role === 'admin' ? 'Quản trị viên hệ thống' : 'Không gian dành cho giảng viên' }}</div>
        </div>
    </aside>
    <div class="app-main">
        <header class="app-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Mở điều hướng"><i class="bi bi-list"></i></button>
                <div><div class="fw-semibold">@yield('title', 'Tổng quan')</div><div class="small text-muted header-date">Quản lý đào tạo · {{ now()->format('d/m/Y') }}</div></div>
            </div>
            <div class="d-flex align-items-center flex-wrap justify-content-end gap-3">
                @foreach ($navigation->where('vi_tri', 'header') as $item)
                    <a class="small text-decoration-none" href="{{ $item->url }}">{{ $item->ten_hien_thi }}</a>
                @endforeach
                <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none text-reset">
                    <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <div><div class="account-name">{{ auth()->user()->name }}</div><div class="account-role">{{ auth()->user()->role === 'admin' ? 'Quản trị viên' : 'Giảng viên' }}</div></div>
                </a>
                <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-secondary btn-sm" title="Đăng xuất"><i class="bi bi-box-arrow-right"></i><span class="d-none d-xl-inline ms-1">Đăng xuất</span></button></form>
            </div>
        </header>
        <main class="app-content">
            @if (session('success'))<div class="alert alert-success" role="status"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
            @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Vui lòng kiểm tra dữ liệu.</strong><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
        <footer class="app-footer"><span>StudentManagement · Hệ thống quản lý đào tạo</span><span>{{ now()->year }} · Kết nối tri thức</span></footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
