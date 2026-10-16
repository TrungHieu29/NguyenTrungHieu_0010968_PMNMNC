@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân')
@section('content')
<h1 class="page-title h3 mb-1">Hồ sơ cá nhân</h1><p class="text-muted mb-4">Cập nhật thông tin và bảo vệ tài khoản của bạn.</p>
<div class="row g-4">
<div class="col-xl-4"><div class="card"><div class="card-body text-center py-5"><span class="avatar mx-auto mb-3" style="width:72px;height:72px;font-size:1.8rem">{{ mb_strtoupper(mb_substr($user->name,0,1)) }}</span><h2 class="h5 fw-bold">{{ $user->name }}</h2><p class="text-muted">{{ $user->email }}</p><span class="badge text-bg-primary">{{ $user->role === 'admin' ? 'Quản trị viên' : 'Giảng viên' }}</span><div class="small text-muted mt-4">Tham gia ngày {{ $user->created_at?->format('d/m/Y') }}</div></div></div></div>
<div class="col-xl-8"><div class="card mb-4"><div class="card-body"><h2 class="h5 fw-bold mb-4"><i class="bi bi-person me-2"></i>Thông tin tài khoản</h2><form method="post" action="{{ route('profile.update') }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label" for="name">Họ tên</label><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $user?->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label class="form-label" for="username">Tên đăng nhập</label><input class="form-control @error('username') is-invalid @enderror" id="username" name="username" type="text" value="{{ old('username', $user?->username) }}" required autocomplete="username">@error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
</div><button class="btn btn-primary mt-4">Lưu thông tin</button></form></div></div>
<div class="card"><div class="card-body"><h2 class="h5 fw-bold mb-2"><i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu</h2><p class="small text-muted mb-4">Sử dụng mật khẩu có ít nhất 8 ký tự để bảo vệ tài khoản.</p><form method="post" action="{{ route('profile.password') }}">@csrf @method('PUT')
<div class="row g-3">
<div class="col-md-6"><label class="form-label" for="current_password">Mật khẩu hiện tại</label><input class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password"  required autocomplete="current-password">@error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label class="form-label" for="password">Mật khẩu mới</label><input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password"  required minlength="8" autocomplete="new-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label class="form-label" for="password_confirmation">Xác nhận mật khẩu mới</label><input class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" type="password"  required autocomplete="new-password">@error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
</div><button class="btn btn-primary mt-4">Cập nhật mật khẩu</button></form></div></div></div></div>
@endsection
