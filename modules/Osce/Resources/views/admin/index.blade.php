@extends('osce::admin.layouts.admin_index')

@section('content')
	<h1>Hello World</h1>
	<p>
		This view is loaded from module: {!! config('osce.name') !!}
	</p>
@stop