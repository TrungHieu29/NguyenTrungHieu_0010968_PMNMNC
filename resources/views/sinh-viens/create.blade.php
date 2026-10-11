@extends('layouts.app')
@section('title', 'Thêm sinh viên')
@section('content')
<h1 class="page-title h3 mb-4">Thêm sinh viên</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('sinh-viens.store') }}">@csrf @include('sinh-viens._form')</form></div></div>
@endsection
