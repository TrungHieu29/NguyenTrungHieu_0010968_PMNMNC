@extends('layouts.app')
@section('title', 'Thêm lớp học')
@section('content')
<h1 class="page-title h3 mb-4">Thêm lớp học</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('lop-hocs.store') }}">@csrf @include('lop-hocs._form')</form></div></div>
@endsection
