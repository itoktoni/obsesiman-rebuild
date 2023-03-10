<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{{ config('app.name', 'Laravel') }}</title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ url('assets/media/image/favicon.png') }}" />

	<!-- Plugin styles -->
	<link rel="stylesheet" href="{{ url('vendors/bundle.css') }}" type="text/css">

	<!-- App styles -->
	<link rel="stylesheet" href="{{ url('assets/css/app.min.css') }}" type="text/css">
</head>

<body class="form-membership">

	<div class="form-wrapper">

		<!-- logo -->
		<div id="logo">
			<img style="max-width: 200px;margin-bottom:30px;padding-top:20px" class="logo"
				src="{{ env('APP_LOGO') ? url('storage/'.env('APP_LOGO')) : url('assets/media/image/logo.png') }}">
		</div>
		<!-- ./ logo -->

		@yield('content')

	</div>

	<!-- App scripts -->
	<script src="{{ url('assets/js/app.min.js') }}"></script>
</body>

</html>