@extends('layout')
@section('title', $queues[$current]['label'])

@section('content')
    <div class="queue-head">
        <nav class="queues" aria-label="Filas">
            @foreach ($queues as $key => $queue)
                <a href="{{ route('tickets.index', ['fila' => $key]) }}" @if ($key === $current) aria-current="page" @endif @class(['alert' => $key === 'breached' && $queue['count'] > 0])>
                    <b>{{ $queue['count'] }}</b> {{ $queue['label'] }}
                </a>
            @endforeach
        </nav>

        <form class="search" role="search" action="{{ route('tickets.index') }}">
            <input type="hidden" name="fila" value="{{ $current }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="Assunto, descrição ou número" aria-label="Buscar chamados">
            <button class="btn">Buscar</button>
        </form>
    </div>

    @if ($tickets->isEmpty())
        <div class="sheet empty">
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
                <li>
                    <a class="ticket" href="{{ route('tickets.show', $ticket) }}">
                        <span class="num priority-{{ $ticket->priority->value }}">{{ $ticket->number() }}</span>
                        <span class="ticket-main">
                            <strong>{{ $ticket->subject }}</strong>
                            <span>{{ $ticket->requester->name }}, {{ $ticket->category->label() }}</span>
                        </span>
                        <span class="ticket-meta">
                            <span class="status">{{ $ticket->status->label() }}</span>
                            <span>{{ $ticket->assignee?->name ?? 'Sem responsável' }}</span>
                        </span>
                        <span class="sla sla-{{ $ticket->slaState() }}">
                            <b>{{ $ticket->priority->label() }}</b>
                            {{ $ticket->slaLabel() }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ol>

        {{ $tickets->links('pagination') }}
    @endif
@endsection
