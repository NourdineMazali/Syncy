@extends('layout')
@section('content')
    <div class="bs-example" data-example-id="table-within-panel">
        <nav aria-label="...">
            <ul class="pager">
                <li><a href="{{url('/pinterest/boards')}}">Previous</a></li>
                <li><a href="#" onclick="$('#facebook_pages').submit();">Next</a></li>
            </ul>
        </nav>
        <h3>Add Facebook page you manage to PinsBook</h3>

        <div class="panel panel-default">
            <!-- Default panel contents -->

        @if(session('message'))
            {!! session('message') !!}
        @endif
        <!-- Table -->
            <table class="table">
                <thead>
                <tr>
                    <th>Add Facebook pages</th>
                    <th>Facebook page to activate</th>
                </tr>
                </thead>
                {!! Form::open(['url' => 'facebook/configure', 'id' => 'facebook_pages']) !!}
                @foreach($pages as $page)
                    <tr>
                        <td scope="row">
                            <input id="{{$page->id}}"
                                   @if(isset($active_pages[$page->id])) {{"checked"}} @endif type="checkbox"
                                   name="pages[{{$page->id}}]">

                            <img width="30" for="{{$page->id}}" height="30" src="{{getfbPageAvatar($page->id,$token) }}"
                                 alt="{{$page->name}} ">

                            <span for="{{$page->id}}">{{$page->name}}</span>
                        </td>
                        <td>
                            <input @if($page->id == $activated) {{'checked'}} @endif value="{{$page->id}}" type="radio"
                                   name="active_page">
                        </td>
                    </tr>
                @endforeach
                {!! Form::close() !!}
            </table>
        </div>
    </div>
    @include('partials.alert')

@endsection
