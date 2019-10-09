@extends('layout')
@section('content')
    {!! Html::style('css/jquery.datetimepicker.css') !!}
    @if(session('message')) {!! session('message') !!} @endif
    <router-view></router-view>
    <dashboard name="" connect-url="@if(isset($url)){{ $url  }} @endif" :pins="pins" :csrftoken="csrftoken">

    </dashboard>
@endsection