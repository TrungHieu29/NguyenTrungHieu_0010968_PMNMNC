@extends('layouts.app')
@section('title', 'Sửa sinh viên')
@section('content')
<h1 class="page-title h3 mb-4">Sửa sinh viên {{ $sinhVien->ma_sinh_vien }}</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('sinh-viens.update', $sinhVien) }}">@csrf @method('PUT') @include('sinh-viens._form')</form></div></div>
@endsection
