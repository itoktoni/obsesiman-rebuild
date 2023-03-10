@extends(Template::master())

@section('title')
{{ $label }}
@endsection

@section('container')
<div class="container-fluid">
	<div id="errormessages"></div>
    {{ $slot }}
</div>
@endsection
