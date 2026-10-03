@extends('layout')
@section('title', $queues[$current]['label'])

@php
    $icons = ['active' => 'inbox', 'mine' => 'user', 'unassigned' => 'user-x', 'breached' => 'alert', 'done' => 'check', 'all' => 'ticket'];
@endphp

@section('content')
    <header class="page-head">
        <h1>Chamados</h1>
        <form class="search" role="search" action="{{ route('tickets.index') }}">
            <input type="hidden" name="fila" value="{{ $current }}">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Assunto, descrição ou número" aria-label="Buscar chamados">
            <button class="btn">Buscar</button>
        </form>
    </header>

    <nav class="queues" aria-label="Filas">
        @foreach ($queues as $key => $queue)
            <a href="{{ route('tickets.index', ['fila' => $key]) }}" @if ($key === $current) aria-current="page" @endif @class(['alert' => $key === 'breached' && $queue['count'] > 0])>
                <x-icon :name="$icons[$key]" />
                <span>{{ $queue['label'] }}</span>
                <b>{{ $queue['count'] }}</b>
            </a>
        @endforeach
    </nav>

    @if ($tickets->isEmpty())
        <div class="sheet empty">
            <x-icon name="inbox" />
            @if ($search !== '')
                <h2>Nenhum chamado encontrado para “{{ $search }}”.</h2>
                <p>Tente outro termo ou <a href="{{ route('tickets.index', ['fila' => 'all']) }}">veja todos os chamados</a>.</p>
            @else
                <h2>Nada nesta fila.</h2>
                <p>Precisa de ajuda com alguma coisa? <a href="{{ route('tickets.create') }}">Abra um chamado</a>.</p>
            @endif
        </div>
    @else
        <ol class="tickets">
            @foreach ($tickets as $ticket)
                <li style="--i: {{ $loop->index }}">@include('tickets._row')</li>
            @endforeach
        </ol>

        {{ $tickets->links('pagination') }}
    @endif
@endsection
