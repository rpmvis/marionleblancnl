
<ul class="topnav" id="myTopnav">
    @foreach($context['menu_items'] as $item)
        <?php $class = $item->active === true? " class='active'" : '';?>

        <li {!! $class !!}><a {!! $class !!} href="{{ $item->href }}">{{ $item->caption }}
            @if ( $item->img_src !== null)
                <img src='{{ $item->img_src }}'>
            @endif
            </a>
        </li>
    @endforeach
    <li class="icon">
        <a href="javascript:void(0);" onclick="click_myTopnav()">☰</a>
    </li>
</ul>