@extends('layout')
@section('title', 'Entrar')
@section('page-class', 'page-auth')

@section('content')
    <div class="auth-hero">
        <a class="brand" href="{{ url('/') }}"><span class="brand-mark"><x-icon name="ticket" /></span> Helpdesk</a>
        <h1>Abra um chamado. Acompanhe até resolver.</h1>
        <p>Cada pedido ganha um número, um responsável e um prazo. Você vê o andamento e responde por aqui.</p>

        {{-- Três senhas empilhadas: a imagem do produto é o próprio chamado. --}}
        <div class="stubs" aria-hidden="true">
            <div class="stub"><b>#0042</b><span>Impressora não imprime</span><i class="sla sla-ok">Vence em 6 horas</i></div>
            <div class="stub"><b>#0043</b><span>Acesso ao ERP bloqueado</span><i class="sla sla-at_risk">Vence em 40 minutos</i></div>
            <div class="stub"><b>#0044</b><span>VPN cai a cada 10 minutos</span><i class="sla sla-met">Resolvido no prazo</i></div>
        </div>
    </div>

    <div class="auth-side">
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
            <button class="btn"><x-icon name="lock" /> Entrar</button>
            <p>Primeira vez aqui? <a href="{{ route('register') }}">Criar conta</a></p>
        </form>

        @if (config('app.demo'))
            <table class="demo">
                <caption>Contas de demonstração, senha <code>password</code></caption>
                <tr><th>Cliente</th><td>cliente@helpdesk.test</td></tr>
                <tr><th>Atendente</th><td>agente@helpdesk.test</td></tr>
                <tr><th>Administrador</th><td>admin@helpdesk.test</td></tr>
            </table>
        @endif
    </div>
@endsection
