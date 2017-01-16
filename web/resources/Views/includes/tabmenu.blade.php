
@if(isset($context['tabmenu_items']))
    @php
        $items = $context['tabmenu_items'];
        $count = count($items);
    @endphp
    @section('tab_menu')
        <ul class="topnav" id="myTabnav">
            @foreach($items as $item)
                <?php $class = $item->active === true? " class='active'" : ''; ?>
                <li {!! $class !!}><a {!! $class !!} href="{{ $item->href }}">{{ $item->caption }}
                        @if ( $item->img_src !== null)
                            <img src='{{ $item->img_src }}'>
                        @endif
                    </a></li>
            @endforeach

            @if ($count > 1)
                <li class="icon">
                    <a href="javascript:void(0);" style="font-size:15px;" onclick="click_myTabnav()">☰</a>
                </li>
            @endif
        </ul>
    @endsection
@endif

