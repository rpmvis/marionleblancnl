@extends('layouts.default')

@include('includes.tabmenu')

@section('content')
    <p>Dank u.</p><br>
    Een email is naar u gestuurd met de volgende inhoud:
    <br>
    <hr>
    @include('pages.visitor_visit_confirmed_html')
    <hr>
@endsection
