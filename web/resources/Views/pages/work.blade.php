<?php
    $locale = $context['locale'];
    $color_items= $context['colormenu_items'];
?>
@extends('layouts.default')

@include('includes.tabmenu')

@section('content')
<div class="work_row">
    {{--image--}}
    <div class="work_col work_content" style="text-align: center" >
        <a href='{{ $context['back_url'] }}'>
            <img src='{{ $work->img_src }}' height='{{{ $work->img_height }}}' width='{{{ $work->img_width }}}'
                 border='0' ALT='{{ $work->img_alt }}'>
        </a>
    </div>
</div>
<div class="work_row">
    <br>&nbsp;
</div>
<div class="work_row">
    <div class="work_col work_content" style="text-align: left">
        <a href='{{ $context['back_url'] }}'>
            {{ $context['back_button_caption'] }}
        </a>
    </div>
    <div class="work_col work_content">
        <b>{{{ $work->titel }}}</b><br>
        <span  class='Tekst_kleiner'>
            {{{ $work->jaar }}}&nbsp;&nbsp;|&nbsp;&nbsp;
            {{{ $work->hoogte }}}&nbsp;x&nbsp;
            {{{ $work->breedte }}}&nbsp;cm&nbsp;&nbsp;|&nbsp;&nbsp;
            {{{ $work->materiaal }}}&nbsp;&nbsp;|&nbsp;&nbsp;
            {{{ $work->beschikbaarheid }}}</span>
    </div>
    <div class="work_col work_content" style="text-align: right">
        <span class='Tekst_kleiner'>{{ $context['select_background_color'] }}</span>
        <br>
        <img class = 'img_background_transp' src='{{ $context['img_src_background_transp'] }}' useMap='#achtergronden'>
        <map name='achtergronden'>
            <area coords=  '0,0, 12,12' href='{{ $color_items['white']->href }}'       shape=RECT alt = '{{ $color_items['white']->alt }}'>
            <area coords= '16,0, 28,12' href='{{ $color_items['soft_yellow']->href }}' shape=RECT alt = '{{ $color_items['soft_yellow']->alt }}'>
            <area coords= '32,0, 44,12' href='{{ $color_items['yellow']->href }}'      shape=RECT alt = '{{ $color_items['yellow']->alt }}'>
            <area coords= '48,0, 60,12' href='{{ $color_items['soft_peach']->href }}'  shape=RECT alt = '{{ $color_items['soft_peach']->alt }}'>
            <area coords= '64,0, 76,12' href='{{ $color_items['peach']->href }}'       shape=RECT alt = '{{ $color_items['peach']->alt }}'>
            <area coords= '80,0, 92,12' href='{{ $color_items['soft_green']->href }}'  shape=RECT alt = '{{ $color_items['soft_green']->alt }}'>
            <area coords= '96,0,108,12' href='{{ $color_items['green']->href }}'       shape=RECT alt = '{{ $color_items['green']->alt }}'>
            <area coords='112,0,124,12' href='{{ $color_items['soft_blue']->href }}'   shape=RECT alt = '{{ $color_items['soft_blue']->alt }}'>
            <area coords='128,0,140,12' href='{{ $color_items['blue']->href }}'        shape=RECT alt = '{{ $color_items['blue']->alt }}'>
        </map>
    </div>
</div>
@endsection
