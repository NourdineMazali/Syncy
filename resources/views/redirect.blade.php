@extends('WelcomeLayout')
@section('content')

    <div class="starter-template">
        <div class="row">
            <button onclick="location.href='{{$url}}'" type="button" class="btn btn-default btn-lg facebook custom">
                <span>Connect with your Facebook account</span>
            </button>
        </div>
    </div>
@endsection