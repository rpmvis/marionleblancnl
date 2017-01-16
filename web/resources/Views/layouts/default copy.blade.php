<!doctype html>
<html>
    <head>
        @include('includes.head')
    </head>
    <body>
        <div>
            <header class="row">
                @include('includes.header')
            </header>
        </div>
        <div class="container">
            <div id="menu">
                @include('includes.menu')
            </div>

            {{--Dit is wat default tekst.--}}

            <div class="container">
                @yield('content')
            </div>
            {{--<p>dit is de body van de default page</p>--}}
            {{--<div id="main" class="row">--}}
                {{--@yield('content')--}}
            {{--</div>--}}
            {{--<footer class="row">--}}
                {{--@include('includes.footer')--}}
            {{--</footer>--}}
            <scripts>
                @include('includes.scripts')
            </scripts>
        </div>
        <div id='bottom_line'>
            <p style="border-bottom:solid 1px #DEDEDE;"></p>
        </div>
    </body>

</html>