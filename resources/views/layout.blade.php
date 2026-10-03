<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Chamados') · Helpdesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' fill='%231b3bd0'/%3E%3Cpath d='M9 8v16M23 8v16M9 16h14' stroke='%23ffcf33' stroke-width='4'/%3E%3C/svg%3E">
</head>
<body>
<header class="band">
    <a class="wordmark" href="{{ url('/') }}">Helpdesk</a>
    @auth
        <nav aria-label="Principal">
            <a href="{{ route('tickets.index') }}" @if (request()->routeIs('tickets.*')) aria-current="page" @endif>Chamados</a>
            @can('admin')
                <a href="{{ route('users.index') }}" @if (request()->routeIs('users.*')) aria-current="page" @endif>Usuários</a>
            @endcan
        </nav>
        <div class="band-end">
            <span class="who">{{ auth()->user()->name }}<small>{{ auth()->user()->role->label() }}</small></span>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn-plain">Sair</button>
            </form>
            <a class="btn-signal" href="{{ route('tickets.create') }}">Abrir chamado</a>
        </div>
    @endauth
</header>

<main class="page @yield('page-class')">
    @if (session('status'))
        <p class="flash" role="status">{{ session('status') }}</p>
    @endif
    @if ($errors->any())
        <ul class="flash flash-error" role="alert">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @yield('content')
</main>
</body>
</html>
