<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Syncy</title>
    <link rel="icon" href="/favicon.png">
    {!! Html::style(asset('css/app.css')) !!}
    {!! Html::style('css/custom.css') !!}
    <script src="/js/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'url'       => $url
        ]) !!};
     </script>
<!-- Styles -->
</head>
<body>
<div class="homepage">
    <div id="app">
    <div class="starter-template">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <router-link class="navbar-brand" to="/">
                    <img class="syncy-logo" style="height: 150%; float: left;" src="/img/loader.png"/>
                </router-link>
                <ul class="nav navbar-nav navbar-right">
                    <li><router-link to="/contact">contact</router-link></li>
                    <li><router-link to="/login">Login</router-link></li>
                </ul>
            </div>
        </nav>

        <div id="particlesJs"></div>