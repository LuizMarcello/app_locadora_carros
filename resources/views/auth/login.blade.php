@extends('layouts.app')

@section('content')
    {{-- @csrf helper do laravel --}}
    {{-- {{ @csrf_token() }} método e outro helper do laravel --}}
    <login-component csrf_token="{{ @csrf_token() }}"></login-component>

@endsection
