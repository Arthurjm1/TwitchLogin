@extends('layout')

@section('content')

<div class="row">
    <div class="col d-flex justify-content-center">
        @if(session('user_already_registered'))
        <h4>Welcome back, {{session('username')}}</h4>
        @else
        <h4>Welcome, {{session('username')}}</h4>
        @endif
    </div>
</div>

@endsection