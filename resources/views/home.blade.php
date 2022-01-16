@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header', 'Welcome, to the shop!')

@section('auth_body')
    <a href="{{ route ('login') }}" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
        <span class="fas fa-sign-in-alt"></span>
        {{ __('adminlte::adminlte.sign_in') }}
    </a>
@stop
