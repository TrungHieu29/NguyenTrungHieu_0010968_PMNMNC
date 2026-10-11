@extends('layouts.app')
@section('title', 'Sửa lớp học')
@section('content')
<h1 class="page-title h3 mb-4">Sửa lớp học {{ $lopHoc->ma_lop }}</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('lop-hocs.update', $lopHoc) }}">@csrf @method('PUT') @include('lop-hocs._form')</form></div></div>
@endsection
