<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @section('head')
        @include('layouts.head')
    @show
</head>
<body class="@yield('body_class','body')">
@section('body')
    @section('header')

    @show
    @section('main')

    @show
    @section('footer')

    @show
@show
</body>
</html>