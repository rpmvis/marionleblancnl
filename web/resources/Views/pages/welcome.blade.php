<?php
    $src = $context['img_blueDot'];
    $img_BlueDot = "<img class='img_BlueDot' src='$src'>";

    $src = $values['img_home'];
    $img_home = "<img src = '$src'. class='img_home' alt='129.jpg'>";
?>

@extends('layouts.default')

@section('content')

    {{--cellpadding = '3' border = '0'--}}
<div class='kop3'>
    {!! $img_BlueDot !!}&nbsp;{{{ $values['welcome.1'] }}}<br><br>
</div>
    <div class = 'div_inline'>
    <div class = 'div_inline'>
        {!! $values['welcome.2'] !!}
    </div>
    <div class = 'div_inline'>
        {!! $img_home !!}
    </div>
</div>
@endsection
