@extends('layout')
@section('title', $ticket->number().' '.$ticket->subject)

@php
    $user = auth()->user();
    $staff = $user->isStaff();
@endphp

@section('content')
    <div class="ticket-head">
        <span class="num num-big priority-{{ $ticket->priority->value }}">{{ $ticket->number() }}</span>
        <div>
            <h1>{{ $ticket->subject }}</h1>
            <p>Aberto por {{ $ticket->requester->name }} em {{ $ticket->created_at->translatedFormat('d \d\e F, H:i') }}</p>
        </div>
    </div>

    <div class="ticket-layout">
        <section class="thread" aria-label="Histórico">
            <article class="sheet msg">
                <header><strong>{{ $ticket->requester->name }}</strong> <time>{{ $ticket->created_at->diffForHumans() }}</time></header>
                <p class="body">{{ $ticket->description }}</p>
                @if ($ticket->attachment_path)
                    <p><a href="{{ route('tickets.attachment', $ticket) }}">Baixar anexo: {{ $ticket->attachment_name }}</a></p>
                @endif
            </article>

            @foreach ($comments as $comment)
                @if ($comment->event)
                    <p class="event">{{ $comment->user->name }} {{ $comment->event }}, {{ $comment->created_at->diffForHumans() }}</p>
                @else
                    <article @class(['sheet', 'msg', 'msg-staff' => $comment->user->isStaff(), 'msg-internal' => $comment->internal])>
                        <header>
                            <strong>{{ $comment->user->name }}</strong>
                            @if ($comment->internal)
                                <span class="tag">Nota interna, o cliente não vê</span>
                            @endif
                            <time>{{ $comment->created_at->diffForHumans() }}</time>
                        </header>
                        <p class="body">{{ $comment->body }}</p>
                    </article>
                @endif
            @endforeach

            @can('reply', $ticket)
                <form class="sheet form" method="post" action="{{ route('tickets.reply', $ticket) }}">
                    @csrf
                    <label>Responder
                        <textarea name="body" rows="4" required maxlength="5000">{{ old('body') }}</textarea>
                    </label>
                    @if (! $staff && $ticket->status === App\Enums\Status::Resolved)
                        <small>Responder reabre o chamado.</small>
                    @endif
                    <div class="form-actions">
                        @if ($staff)
                            <label class="check"><input type="checkbox" name="internal" value="1"> Nota interna, só a equipe vê</label>
                        @endif
                        <button class="btn">Enviar resposta</button>
                    </div>
                </form>
            @else
                <p class="event">Chamado encerrado. Para continuar o assunto, <a href="{{ route('tickets.create') }}">abra um novo chamado</a>.</p>
            @endcan
        </section>

        <aside class="sheet props">
            <dl>
                <dt>Status</dt>
                <dd>{{ $ticket->status->label() }}</dd>
                <dt>Prazo</dt>
                <dd><span class="sla sla-{{ $ticket->slaState() }}">{{ $ticket->slaLabel() }}</span></dd>
                <dt>Prioridade</dt>
                <dd>{{ $ticket->priority->label() }}</dd>
                <dt>Categoria</dt>
                <dd>{{ $ticket->category->label() }}</dd>
                <dt>Responsável</dt>
                <dd>{{ $ticket->assignee?->name ?? 'Sem responsável' }}</dd>
                @if ($ticket->first_response_at)
                    <dt>Primeira resposta</dt>
                    <dd>{{ $ticket->created_at->diffForHumans($ticket->first_response_at, true) }} após a abertura</dd>
                @endif
            </dl>

            {{-- Só aparecem os próximos passos válidos do fluxo, e só os que este usuário pode dar. --}}
            @php
                $steps = collect($ticket->status->next())->filter(fn ($status) => $user->can('changeStatus', [$ticket, $status]));
            @endphp
            @if ($steps->isNotEmpty())
                <form method="post" action="{{ route('tickets.update', $ticket) }}" class="steps">
                    @csrf @method('patch')
                    @foreach ($steps as $status)
                        <button class="btn-outline" name="status" value="{{ $status->value }}">
                            {{ match ($status) {
                                App\Enums\Status::InProgress => $ticket->status->isDone() ? 'Reabrir' : 'Assumir atendimento',
                                App\Enums\Status::WaitingCustomer => 'Aguardar cliente',
                                App\Enums\Status::Resolved => 'Marcar como resolvido',
                                App\Enums\Status::Closed => 'Encerrar',
                                default => $status->label(),
                            } }}
                        </button>
                    @endforeach
                </form>
            @endif

            @if ($staff && $ticket->status !== App\Enums\Status::Closed)
                <form method="post" action="{{ route('tickets.update', $ticket) }}" class="form">
                    @csrf @method('patch')
                    <label>Responsável
                        <select name="assignee_id">
                            <option value="">Sem responsável</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" @selected($ticket->assignee_id === $agent->id)>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Prioridade
                        <select name="priority">
                            @foreach (App\Enums\Priority::cases() as $priority)
                                <option value="{{ $priority->value }}" @selected($ticket->priority === $priority)>{{ $priority->label() }} ({{ $priority->slaHours() }}h)</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="btn">Salvar alterações</button>
                </form>
            @endif
        </aside>
    </div>
@endsection
