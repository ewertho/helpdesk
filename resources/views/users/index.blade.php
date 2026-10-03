@extends('layout')
@section('title', 'Usuários')
@section('page-class', 'page-narrow')

@section('content')
    <header class="page-head">
        <h1>Usuários</h1>
        <p class="lead">Atendentes veem e respondem todos os chamados. Administradores também definem papéis.</p>
    </header>

    <ul class="sheet users">
        @foreach ($users as $user)
            <li>
                <x-avatar :user="$user" />
                <span class="who">{{ $user->name }}<small>{{ $user->email }}</small></span>
                @if ($user->is(auth()->user()))
                    <span class="muted">{{ $user->role->label() }} (você)</span>
                @else
                    <form method="post" action="{{ route('users.update', $user) }}">
                        @csrf @method('patch')
                        <select name="role" aria-label="Papel de {{ $user->name }}">
                            @foreach (App\Enums\Role::cases() as $role)
                                <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                            @endforeach
                        </select>
                        <button class="btn-outline">Salvar papel</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>

    {{ $users->links('pagination') }}
@endsection
