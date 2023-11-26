<!doctype html>
<html lang="en" moznomarginboxes mozdisallowselectionprint>

<head>
@livewireStyles
<link rel="stylesheet" href="{{ url('assets/css/print.css') }}" type="text/css">
</head>

<body>
    @yield('header')
    @yield('content')
</body>
@livewireScripts

</html>