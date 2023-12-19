<!doctype html>
<html lang="en">

<head>
    @include('layouts.meta')
    @yield('head')

    <link rel="stylesheet" href="{{ url('assets/css/app.min.css') }}" type="text/css">
    @yield('css')
    @livewireStyles
</head>

<body class="fixed">

    @include('layouts.header')

    <div id="main">

        <div class="navigation">

            @include('layouts.left')

        </div>

        <div class="main-content" id="content">
            @yield('container')
            @include('layouts.alert')
        </div>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" id="modal-body">
                </div>
            </div>
        </div>

    </div>

    <script src="{{ url('assets/js/app.min.js') }}"></script>

    @stack('footer')
    @livewireScripts
</body>

</html>
