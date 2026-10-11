@extends('layouts.app')
@section('title', 'Thêm Menu')
@section('content')
<h1 class="page-title h3 mb-4">Thêm Menu</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('menus.store') }}">@csrf @include('menus._form')</form></div></div>
@endsection
