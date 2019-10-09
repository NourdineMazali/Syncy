@extends('layout')
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('message'))
        {!! session('message') !!}
    @endif

    @if(!is_null($email))
        <div class="alert alert-success" role="alert">Your Instagram account is successfully configured</div>
    @endif

    {!! Form::open(['action' => 'Controller@storeInstagramCredentials', 'class' => "form-signin"]) !!}

    <h4 class="form-signin-heading">Instagram Email and Password</h4>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" value="{{$email}}" name="email" id="inputEmail" class="form-control" placeholder="Email address"
           required
           autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" value="{{$password}}" name="password" id="inputPassword" class="form-control"
           placeholder="Password" required>
    <div class="checkbox">
        <label>
            <span> The Instagram Credentials Are Stored in a Hashed Format </span>
        </label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Save</button>
    {!! Form::close() !!}

@endsection