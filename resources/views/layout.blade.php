<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Chamados') · Helpdesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%231b3bd0'/%3E%3Cpath d='M7 12a4 4 0 0 1 0 8v3h18v-3a4 4 0 0 1 0-8V9H7z' fill='%23ffcf33'/%3E%3C/svg%3E">
</head>
<body class="@auth app @else guest @endauth">
@auth
    <aside class="rail">
        <a class="brand" href="{{ url('/') }}">
            <span class="brand-mark"><x-icon name="ticket" /></span>
            Helpdesk
        </a>

        <nav aria-label="Principal">
            @can('staff')
                <a href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif><x-icon name="dashboard" /> Painel</a>
            @endcan
            <a href="{{ route('tickets.index') }}" @if (request()->routeIs('tickets.*')) aria-current="page" @endif><x-icon name="inbox" /> Chamados</a>
            @can('staff')
                <a href="{{ route('canned.index') }}" @if (request()->routeIs('canned.*')) aria-current="page" @endif><x-icon name="zap" /> Respostas prontas</a>
            @endcan
            @can('admin')
                <a href="{{ route('users.index') }}" @if (request()->routeIs('users.*')) aria-current="page" @endif><x-icon name="users" /> Usuários</a>
            @endcan
        </nav>

        <a class="btn-signal rail-cta" href="{{ route('tickets.create') }}"><x-icon name="plus" /> Abrir chamado</a>

        <div class="rail-user">
            <x-avatar :user="auth()->user()" />
            <span class="who">{{ auth()->user()->name }}<small>{{ auth()->user()->role->label() }}</small></span>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="icon-btn" title="Sair" aria-label="Sair"><x-icon name="logout" /></button>
            </form>
        </div>
    </aside>
@endauth

<main class="page @yield('page-class')">
    @if (session('status'))
        <p class="flash" role="status"><x-icon name="check" /> {{ session('status') }}</p>
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
