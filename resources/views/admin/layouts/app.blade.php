<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}">
    
    <title>@yield('title') - {{config('app.name')}}</title>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto py-8">
        @yield('content')
        <br>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="route('logout')"
                onclick="event.preventDefault();this.closest('form').submit();">
                {{ __('Logout') }}<i class="fas fa-sign-out-alt p-3"></i>
            </a>
        </form>
        <div class="grid">
            <a href="{{ route('posts.index') }}" class="my-4 uppercase px-8 py-2 rounded bg-green-600 text-blue-50 max-w-max shadow-sm hover:shadow-lg"><i class="fas fa-home text-white"></i> Home</a>
        </div>
    </div>
</body>
</html>