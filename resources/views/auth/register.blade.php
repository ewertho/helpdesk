@extends('layout')
@section('title', 'Criar conta')
@section('page-class', 'page-auth')

@section('content')
    <div class="auth-intro">
        <h1>Crie sua conta para abrir chamados.</h1>
        <p>Em menos de um minuto você registra o primeiro pedido e passa a receber as respostas da equipe.</p>
    </div>

    <form class="sheet form" method="post" action="{{ url('/register') }}">
        @csrf
        <h2>Criar conta</h2>
        <label>Nome
            <input name="name" value="{{ old('name') }}" required minlength="2" maxlength="80" autofocus autocomplete="name">
        </label>
        <label>E-mail
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        </label>
        <label>Senha
            <input type="password" name="password" required minlength="8" autocomplete="new-password">
            <small>Pelo menos 8 caracteres.</small>
        </label>
        <label>Repita a senha
            <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </label>
        <button class="btn">Criar conta</button>
        <p>Já tem conta? <a href="{{ route('login') }}">Entrar</a></p>
    </form>
@endsection
