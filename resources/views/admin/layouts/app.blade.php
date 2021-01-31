<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>@yield('title') - {{config('app.name')}}</title>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto py-8">
        @yield('content')
        <hr>
        <button><a href="{{route('posts.index')}}">Home</a></button>
    </div>
</body>
</html>