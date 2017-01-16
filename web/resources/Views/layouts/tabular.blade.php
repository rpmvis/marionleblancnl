<?php
$form_type =$context['form_type'];
$src = $context['img_blueDot'];
$img_BlueDot = "<img class='img_BlueDot' src='$src'>";
$header = $values['table_header'];
$field_names = $values['table_field_names'];
$rows = $values['table_rows'];

$max_width_column1 = '';

if (count($field_names) > 1){
    switch ($form_type){
        case 'welcome': $width = 300; break;
        case 'about_the_work.literature': $width = 350; break;
        default: $width = 85; break;
    }
    if ($width > 0)
        $max_width_column1 = ".max-width-column1 {  max-width: ".(string)$width."px ;}";
}
?>

@extends('layouts.default')

@include('includes.tabmenu')

@section('content')
    <style>
        {{ $max_width_column1 }}
    </style>

<div class='kop3'>
    <pre>{!! $img_BlueDot !!}&nbsp;{{{ $header }}}</pre>
</div>
{{--render rows like so:--}}
{{--<div class='grid-row'>--}}
{{--<div class='grid-item max-width-column1'>--}}
{{--t/m heden--}}
{{--</div>--}}
{{--<div class='grid-item'>--}}
{{--Particulieren--}}
{{--</div>--}}
{{--</div>--}}

@foreach ($rows as $row)
<?php $count = 0; ?>
<div class='grid-row'>

    @foreach ($field_names as $name)
    <?php
        $count +=1;
        $class = ($count ==1)? ' max-width-column1': '';
    ?>
    <div class="grid-item{{ $class }}">{!! $row[$name] !!}</div>
    @endforeach
</div>
@endforeach

@endsection

