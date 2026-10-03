@extends('layout')
@section('title', 'Entrar')
@section('page-class', 'page-auth')

@section('content')
    <div class="auth-intro">
        <h1>Abra um chamado. Acompanhe até resolver.</h1>
        <p>Cada pedido ganha um número, um responsável e um prazo. Você vê o andamento e responde por aqui.</p>

        @if (config('app.demo'))
            <table class="demo">
                <caption>Contas de demonstração, senha <code>password</code></caption>
                <tr><th>Cliente</th><td>cliente@helpdesk.test</td></tr>
                <tr><th>Atendente</th><td>agente@helpdesk.test</td></tr>
                <tr><th>Administrador</th><td>admin@helpdesk.test</td></tr>
            </table>
        @endif
    </div>

    <form class="sheet form" method="post" action="{{ url('/login') }}">
        @csrf
        <h2>Entrar</h2>
        <label>E-mail
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        </label>
        <label>Senha
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <label class="check"><input type="checkbox" name="remember" value="1"> Manter conectado</label>
        <button class="btn">Entrar</button>
        <p>Primeira vez aqui? <a href="{{ route('register') }}">Criar conta</a></p>
    </form>
@endsection
