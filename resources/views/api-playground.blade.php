@extends('layouts.app')
@section('title', 'API Playground')
@section('content')
<h1 class="page-title h3 mb-2">API Playground</h1>
<p class="text-muted mb-4">Thử REST API bằng phiên đăng nhập hiện tại. Các lệnh xóa chỉ dành cho admin.</p>
<div class="card mb-4"><div class="card-body"><form id="api-form" class="row g-3">
<div class="col-md-6"><label class="form-label" for="endpoint">Endpoint</label><select class="form-select" id="endpoint">
@foreach (['lop-hocs' => 'Lớp học', 'sinh-viens' => 'Sinh viên'] as $resource => $label)
<option value="GET|/api/{{ $resource }}">GET · Danh sách {{ $label }}</option>
<option value="GET|/api/{{ $resource }}/{id}">GET · Chi tiết {{ $label }}</option>
<option value="POST|/api/{{ $resource }}">POST · Thêm {{ $label }}</option>
<option value="PUT|/api/{{ $resource }}/{id}">PUT · Cập nhật {{ $label }}</option>
<option value="PATCH|/api/{{ $resource }}/{id}">PATCH · Cập nhật một phần {{ $label }}</option>
<option value="DELETE|/api/{{ $resource }}/{id}">DELETE · Xóa {{ $label }}</option>
@endforeach
</select></div>
<div class="col-md-3"><label class="form-label" for="resource-id">ID (nếu endpoint cần)</label><input class="form-control" id="resource-id" type="number" min="1" placeholder="Ví dụ: 1"></div>
<div class="col-md-3"><label class="form-label" for="query">Query parameters</label><input class="form-control" id="query" placeholder="search=ABC&direction=asc"></div>
<div class="col-12"><label class="form-label" for="body">JSON body (POST, PUT, PATCH)</label><textarea class="form-control font-monospace" id="body" rows="8" placeholder='{"ten_lop":"Lớp A","ma_lop":"A01","si_so":30,"giao_vien":"Giáo viên"}'></textarea></div>
<div class="col-12"><button class="btn btn-primary" type="submit" id="execute">Execute</button></div>
</form></div></div>
<div class="card"><div class="card-body"><h2 class="h5">Response <span id="response-status" class="badge text-bg-secondary">Chưa gửi</span></h2><pre id="response-body" class="bg-light rounded p-3 mb-0 text-wrap" style="min-height:8rem">Chọn endpoint rồi nhấn Execute.</pre></div></div>
<script>
document.getElementById('api-form').addEventListener('submit', async function (event) {
    event.preventDefault();
    const [method, template] = document.getElementById('endpoint').value.split('|');
    const id = document.getElementById('resource-id').value.trim();
    const query = document.getElementById('query').value.trim().replace(/^\?/, '');
    const output = document.getElementById('response-body');
    const badge = document.getElementById('response-status');
    if (template.includes('{id}') && !/^\d+$/.test(id)) { output.textContent = 'Vui lòng nhập ID hợp lệ.'; return; }
    const url = template.replace('{id}', encodeURIComponent(id)) + (query ? '?' + query : '');
    const options = { method, credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } };
    if (['POST', 'PUT', 'PATCH'].includes(method)) {
        try { options.body = JSON.stringify(JSON.parse(document.getElementById('body').value || '{}')); }
        catch (_) { output.textContent = 'JSON body không hợp lệ.'; return; }
        options.headers['Content-Type'] = 'application/json';
    }
    output.textContent = 'Đang gửi yêu cầu...';
    try {
        const response = await fetch(url, options);
        badge.textContent = response.status + ' ' + response.statusText;
        badge.className = 'badge ' + (response.ok ? 'text-bg-success' : 'text-bg-danger');
        const raw = await response.text();
        try { output.textContent = JSON.stringify(JSON.parse(raw), null, 2); }
        catch (_) { output.textContent = raw || '(Không có nội dung)'; }
    } catch (error) {
        badge.textContent = 'Lỗi kết nối'; badge.className = 'badge text-bg-danger';
        output.textContent = error.message;
    }
});
</script>
@endsection
