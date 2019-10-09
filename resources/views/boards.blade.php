@extends('layout')
@section('content')
    <div class="jumbotron">
        <nav aria-label="...">
            <ul class="pager">
                <li><a href="#">Previous</a></li>
                <li><a href="#" onclick=" $('#pinterest_boards').submit();">Next</a></li>
            </ul>
        </nav>
        {!! Form::open(['action' => 'Controller@savePinterestBoard', 'id' => 'pinterest_boards']) !!}
        <h3>Choose your Pinterest board</h3>
        @foreach($all_boards as $row => $board)
            @if($row % 6 == 0) @if($row>0) {!! '</div>' !!}@endif {!! '<div class="row">' !!} @endif

            <div class="col-xs-6 col-md-2 @if($board->id == $active_board) {{'activated'}} @endif">
                <a href="#" class="thumbnail">
                    <img src="{{!is_null($board->image->large->url) ? $board->image->large->url : '/img/pinterest.png'  }}"
                         alt="...">
                </a>
                <h5><label for="{{$board->id}}"> {!! $board->name !!} </label>
                    <input type="radio" @if($board->id == $active_board) {{'checked'}} @endif  id="{{$board->id}}"
                           value="{{$board->id}}" name="board">
                </h5>
            </div>

            @if(($row == sizeof($all_boards) ) ) {!!  '</div>' !!}  @endif

            @endforeach
        {!! Form::close() !!}
    </div>
@endsection
