<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>Syncy</title>
    <link rel="icon" href="/favicon.png">
    {{--Style and script --}}
    {!! Html::style(asset('css/app.css')) !!}
    {!! Html::style('css/custom.css') !!}
    {!! Html::style('css/jquery-ui.css') !!}
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token(), 'url' => isset($url)?$url: '' ]) !!};
    </script>
<!-- Styles -->
</head>
<body class="dashboard-body">
<div id="app">
    <nav class="navbar navbar-default dashboard-header">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{url('/dashboard')}}">
                    <img id="header-img" src="/img/loader.png">
                </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">Go to <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><router-link to="/profile" >Profile</router-link></li>
                            <li><router-link to="/facebook/pages" >Facebook Pages</router-link></li>
                            <li><router-link to="/pinterest/boards">Pinterest Boards</router-link></li>
                            <li><router-link to="/instagram">Instagram Account</router-link></li>
                            <li><a href="{{url('/logout')}}">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <div class="container">