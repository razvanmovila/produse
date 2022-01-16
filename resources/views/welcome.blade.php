@extends('adminlte::page')

@section('title', 'Welcome')

@section('content_header')
    <h1>Welcome</h1>
@stop

@section('content')
    <x-adminlte-button label="Button"/>
    <p>Welcome to this beautiful admin panel.</p>
    <x-adminlte-button class="btn-lg" type="reset" label="Reset" theme="outline-danger" icon="fas fa-lg fa-trash"></x-adminlte-button>
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
