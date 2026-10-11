<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StudentManagement') · StudentManagement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f7fb; color:#1b2b45; }
        .sidebar { background:#102845; min-height:100vh; }
        .sidebar .nav-link { color:#d3ddec; border-radius:.6rem; padding:.7rem .9rem; }
        .sidebar .nav-link:hover,.sidebar .nav-link.active { background:#254669; color:#fff; }
        .card { border:0; box-shadow:0 5px 24px rgba(16,40,69,.07); border-radius:1rem; }
        .table th { color:#627087; font-size:.85rem; white-space:nowrap; }
        .table td { vertical-align:middle; }
        .page-title { font-weight:700; letter-spacing:-.02em; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-12 col-lg-2 sidebar p-3">
            <div class="text-white fs-5 fw-bold mb-4">StudentManagement</div>
            @php
                $sidebarMenus = \App\Models\Menu::where('vi_tri', 'sidebar')->where('trang_thai', true)->orderBy('thu_tu')->orderBy('id')->get();
            @endphp
            <nav class="nav flex-column gap-1">
                @foreach ($sidebarMenus as $item)
                    @if ($item->isVisibleTo(auth()->user()))
                        <a class="nav-link {{ request()->is(ltrim($item->url, '/')) ? 'active' : '' }}" href="{{ $item->url }}">{{ $item->ten_hien_thi }}</a>
                    @endif
                @endforeach
                @if (auth()->user()->role === 'admin' && ! $sidebarMenus->contains(fn ($item) => $item->url === '/menus' && $item->isVisibleTo(auth()->user())))
                    <a class="nav-link" href="{{ route('menus.index') }}">Quản lý Menu</a>
                @endif
            </nav>
        </aside>
        <div class="col-12 col-lg-10 p-0 d-flex flex-column" style="min-height:100vh">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center flex-wrap gap-3"><span class="text-muted">Quản lý học sinh · {{ now()->format('d/m/Y') }}</span>
                    @foreach (\App\Models\Menu::where('vi_tri', 'header')->where('trang_thai', true)->orderBy('thu_tu')->get() as $item)
                        @if ($item->isVisibleTo(auth()->user()))
                            <a class="small text-decoration-none" href="{{ $item->url }}">{{ $item->ten_hien_thi }}</a>
                        @endif
                    @endforeach
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span><strong>{{ auth()->user()->name }}</strong> <span class="badge text-bg-primary">{{ strtoupper(auth()->user()->role) }}</span></span>
                    <form method="post" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-secondary btn-sm">Đăng xuất</button></form>
                </div>
            </header>
            <main class="container-fluid p-4 flex-grow-1">
                @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
                @if ($errors->any()) <div class="alert alert-danger"><strong>Vui lòng kiểm tra dữ liệu:</strong><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
                @yield('content')
            </main>
            <footer class="text-center text-muted py-3 small">StudentManagement · Laravel</footer>
        </div>
    </div>
</div>
</body>
</html>
