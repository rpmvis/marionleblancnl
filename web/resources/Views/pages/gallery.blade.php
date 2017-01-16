<?php
    $count = 0;
    $total = count($icons);
?>

@extends('layouts.default')

@include('includes.tabmenu')

@section('content')
@if (!empty($icons))
    @foreach ($icons as $icon)
        <?php $count +=1; ?>

        {{--open row tag --}}
        @if (($count-1) % 4 === 0 )
            <div class='icon_row'>
        @endif

        {{--show icon and icon title--}}
        <div class="icon_col icon_content">
            <a href='{{ $icon->url }}'>
                <img src='{{ $icon->src }}' height='{{ $icon->height }}'
                     width='{{ $icon->width }}' border='0'>
            </a>
            <p>{{ $icon->title }}</p>
        </div>

        {{--close row tag--}}
        @if ( $count % 4 === 0 || $count === $total)
            </div>
        @endif
    @endforeach

@else
    {{"No data found."}}
@endif

@endsection
