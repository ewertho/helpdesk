@extends('layout')
@section('title', $ticket->number().' '.$ticket->subject)

@php
    use App\Enums\Status;

    $user = auth()->user();
    $staff = $user->isStaff();
    // Trilha do chamado: "aguardando cliente" é um desvio de "em atendimento", não uma etapa a mais.
    $track = [Status::Open, Status::InProgress, Status::Resolved, Status::Closed];
    $position = array_search($ticket->status === Status::WaitingCustomer ? Status::InProgress : $ticket->status, $track, true);
    $steps = collect($ticket->status->next())->filter(fn ($status) => $user->can('changeStatus', [$ticket, $status]));
@endphp

@section('content')
    <a class="back" href="{{ route('tickets.index') }}">Chamados</a>

    <header class="ticket-head priority-{{ $ticket->priority->value }}">
        <span class="stub-num stub-big"><b style="view-transition-name: ticket-{{ $ticket->id }}">{{ $ticket->number() }}</b></span>
        <div>
            <h1>{{ $ticket->subject }}</h1>
            <p><x-icon :name="$ticket->category->icon()" /> {{ $ticket->category->label() }}, aberto por {{ $ticket->requester->name }} em {{ $ticket->created_at->translatedFormat('d \d\e F, H:i') }}</p>
        </div>
    </header>

    <ol class="track" aria-label="Andamento do chamado">
        @foreach ($track as $i => $status)
            <li @class(['done' => $i < $position, 'now' => $i === $position]) @if ($i === $position) aria-current="step" @endif>
                <span class="dot">@if ($i < $position)<x-icon name="check" />@else{{ $i + 1 }}@endif</span>
                {{ $i === $position ? $ticket->status->label() : $status->label() }}
            </li>
        @endforeach
    </ol>

    <div class="ticket-layout">
        <section class="thread" aria-label="Histórico">
            <article class="sheet msg">
                <header>
                    <x-avatar :user="$ticket->requester" />
                    <strong>{{ $ticket->requester->name }}</strong>
                    <time>{{ $ticket->created_at->diffForHumans() }}</time>
                </header>
                <p class="body">{{ $ticket->description }}</p>
                @if ($ticket->attachment_path)
                    <p><a class="attachment" href="{{ route('tickets.attachment', $ticket) }}"><x-icon name="clip" /> {{ $ticket->attachment_name }}</a></p>
                @endif
            </article>

            @foreach ($comments as $comment)
                @if ($comment->event)
                    <p class="event"><span class="event-dot"></span>{{ $comment->user->name }} {{ $comment->event }}, {{ $comment->created_at->diffForHumans() }}</p>
                @else
                    <article @class(['sheet', 'msg', 'msg-staff' => $comment->user->isStaff(), 'msg-internal' => $comment->internal])>
                        <header>
                            <x-avatar :user="$comment->user" />
                            <strong>{{ $comment->user->name }}</strong>
                            @if ($comment->user->isStaff())
                                <span class="tag tag-team">Equipe</span>
                            @endif
                            @if ($comment->internal)
                                <span class="tag"><x-icon name="lock" /> Nota interna, o cliente não vê</span>
                            @endif
                            <time>{{ $comment->created_at->diffForHumans() }}</time>
                        </header>
                        <p class="body">{{ $comment->body }}</p>
                    </article>
                @endif
            @endforeach

            @can('rate', $ticket)
                <form class="sheet form rating-form" method="post" action="{{ route('tickets.rate', $ticket) }}">
                    @csrf
                    <h2><x-icon name="smile" /> Como foi o atendimento?</h2>
                    {{-- Estrelas em ordem inversa no HTML para o CSS acender "esta e as anteriores" sem JavaScript. --}}
                    <fieldset class="stars">
                        <legend class="sr-only">Nota de 1 a 5</legend>
                        @foreach ([5, 4, 3, 2, 1] as $value)
                            <input type="radio" name="rating" id="star-{{ $value }}" value="{{ $value }}" required>
                            <label for="star-{{ $value }}" title="{{ $value }} de 5"><x-icon name="star" /><span class="sr-only">{{ $value }} de 5</span></label>
                        @endforeach
                    </fieldset>
                    <label>Quer contar o que funcionou ou o que faltou?
                        <textarea name="rating_comment" rows="2" maxlength="500"></textarea>
                    </label>
                    <button class="btn">Enviar avaliação</button>
                </form>
            @endcan

            @can('reply', $ticket)
                <form class="sheet form" method="post" action="{{ route('tickets.reply', $ticket) }}">
                    @csrf
                    @if ($canned->isNotEmpty())
                        <div class="canned" role="group" aria-label="Respostas prontas">
                            <span><x-icon name="zap" /> Respostas prontas</span>
                            @foreach ($canned as $reply)
                                <button type="button" class="chip" data-insert="{{ $reply->body }}">{{ $reply->title }}</button>
                            @endforeach
                        </div>
                    @endif
                    <label>Responder
                        <textarea id="reply-body" name="body" rows="4" required maxlength="5000">{{ old('body') }}</textarea>
                    </label>
                    @if (! $staff && $ticket->status === Status::Resolved)
                        <small>Responder reabre o chamado.</small>
                    @endif
                    <div class="form-actions">
                        @if ($staff)
                            <label class="check"><input type="checkbox" name="internal" value="1"> <x-icon name="lock" /> Nota interna, só a equipe vê</label>
                        @endif
                        <button class="btn"><x-icon name="send" /> Enviar resposta</button>
                    </div>
                </form>
            @else
                <p class="event"><span class="event-dot"></span>Chamado encerrado. Para continuar o assunto, <a href="{{ route('tickets.create') }}">abra um novo chamado</a>.</p>
            @endcan
        </section>

        <aside class="sheet props">
            <div class="sla-box sla-{{ $ticket->slaState() }}">
                <x-icon :name="$ticket->slaState() === 'breached' ? 'alert' : ($ticket->resolved_at ? 'check' : 'clock')" />
                <strong>{{ $ticket->slaLabel() }}</strong>
                <span class="meter meter-{{ $ticket->slaState() }}"><i style="--fill: {{ round($ticket->slaProgress() * 100) }}%"></i></span>
                <small>Prazo de {{ $ticket->priority->slaHours() }}h, até {{ $ticket->due_at->translatedFormat('d/m \à\s H:i') }}</small>
            </div>

            <dl>
                <dt>Prioridade</dt>
                <dd class="prio prio-{{ $ticket->priority->value }}"><x-icon :name="$ticket->priority->icon()" /> {{ $ticket->priority->label() }}</dd>
                <dt>Responsável</dt>
                <dd>
                    @if ($ticket->assignee)
                        <x-avatar :user="$ticket->assignee" class="avatar avatar-sm" /> {{ $ticket->assignee->name }}
                    @else
                        <span class="muted">Sem responsável</span>
                    @endif
                </dd>
                @if ($ticket->first_response_at)
                    <dt>Primeira resposta</dt>
                    <dd>{{ $ticket->created_at->diffForHumans($ticket->first_response_at, true) }} após a abertura</dd>
                @endif
                @if ($ticket->rating)
                    <dt>Avaliação</dt>
                    <dd class="rated" aria-label="Nota {{ $ticket->rating }} de 5">
                        @for ($i = 1; $i <= 5; $i++)<x-icon name="star" @class(['icon', 'on' => $i <= $ticket->rating]) />@endfor
                    </dd>
                @endif
            </dl>
            @if ($ticket->rating_comment)
                <p class="quote">“{{ $ticket->rating_comment }}”</p>
            @endif

            {{-- Só aparecem os próximos passos válidos do fluxo, e só os que este usuário pode dar. --}}
            @if ($steps->isNotEmpty())
                <form method="post" action="{{ route('tickets.update', $ticket) }}" class="steps">
                    @csrf @method('patch')
                    @foreach ($steps as $status)
                        <button @class(['btn' => $status === Status::Resolved, 'btn-outline' => $status !== Status::Resolved]) name="status" value="{{ $status->value }}">
                            {{ match ($status) {
                                Status::InProgress => $ticket->status->isDone() ? 'Reabrir' : 'Assumir atendimento',
                                Status::WaitingCustomer => 'Aguardar cliente',
                                Status::Resolved => 'Marcar como resolvido',
                                Status::Closed => 'Encerrar',
                                default => $status->label(),
                            } }}
                        </button>
                    @endforeach
                </form>
            @endif

            @if ($staff && $ticket->status !== Status::Closed)
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
                    <button class="btn-outline">Salvar alterações</button>
                </form>
            @endif
        </aside>
    </div>

    @if ($canned->isNotEmpty())
        <script>
            // Resposta pronta: insere o texto no cursor e devolve o foco para a pessoa terminar de escrever.
            document.querySelectorAll('[data-insert]').forEach((button) => button.addEventListener('click', () => {
                const field = document.getElementById('reply-body');
                field.setRangeText(button.dataset.insert, field.selectionStart, field.selectionEnd, 'end');
                field.focus();
            }));
        </script>
    @endif
@endsection
