
{{--Only show homepage view --}}
{{--@if(isset($active_page))--}}
{{--<li class="dropdown">--}}
{{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">--}}
{{--<span class="label label-danger facebook">{{ isset($pages[$active_page]) ? "Share to : ". $pages[$active_page]['name'] : ''  }} </span>--}}
{{--<span class="caret"></span></a>--}}
{{--<ul class="dropdown-menu">--}}

{{--@foreach($pages as $page)--}}
{{--<li>--}}
{{--<a href="{{ url("/facebook/switch/".$page['page_id']) }}">--}}
{{--{{ $page['name'] }}--}}
{{--</a>--}}
{{--</li>--}}
{{--@endforeach--}}
{{--</ul>--}}
{{--</li>--}}
{{--@endif--}}
{{--@if(isset($active_board) && isset($boards))--}}
{{--<li class="dropdown">--}}
{{--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"--}}
{{--aria-haspopup="true"--}}
{{--aria-expanded="false">--}}
{{--<span class="label label-danger pinterest"> Synchronize From : {{ isset($boards[$active_board]) ? $boards[$active_board] : ''  }} </span>--}}
{{--<span class="caret"></span></a>--}}
{{--<ul class="dropdown-menu">--}}

{{--@foreach($boards as $board => $name)--}}
{{--<li>--}}
{{--<a href="{{ url("/pinterest/switch/".$board) }}">--}}
{{--{{ $name }}--}}
{{--</a>--}}
{{--</li>--}}
{{--@endforeach--}}

{{--</ul>--}}
{{--</li>--}}
{{--@endif--}}
{{--</li>--}}