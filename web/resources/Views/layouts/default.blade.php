<?php
if (isset($context['bgcolor'])) $bgcolor = $context['bgcolor'];
else $bgcolor = 'white';
?>

        <!doctype html>

<head>
    @include('includes.head')
</head>

<body>
    <div id='main'>
        <leftbox></leftbox>
        <mainbox>
            <header>
                @include('includes.header')
            </header>
            <div style="background-color: {{ $bgcolor }}">
                <div>
                    @include('includes.menu')
                </div>
                <div>
                    @yield('tab_menu')
                </div>
                <div class="padding_bottom_menu">
                </div>
                <div>
                    @yield('content')
                </div>
            </div>

            <footer>
                @include('includes.footer')
            </footer>
        </mainbox>
        <rightbox></rightbox>
    </div>
</body>
