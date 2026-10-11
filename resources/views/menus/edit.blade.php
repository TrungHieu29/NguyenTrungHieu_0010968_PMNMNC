@extends('layouts.app')
@section('title', 'Sửa Menu')
@section('content')
<h1 class="page-title h3 mb-4">Sửa Menu {{ $menu->ten_hien_thi }}</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('menus.update', $menu) }}">@csrf @method('PUT') @include('menus._form')</form></div></div>
@endsection
