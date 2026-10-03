{{-- Um chamado como senha de atendimento: canhoto com o número, corpo com o assunto, prazo à direita. --}}
<a class="ticket priority-{{ $ticket->priority->value }}" href="{{ route('tickets.show', $ticket) }}">
    <span class="stub-num">
        <b style="view-transition-name: ticket-{{ $ticket->id }}">{{ $ticket->number() }}</b>
        <small><x-icon :name="$ticket->priority->icon()" /> {{ $ticket->priority->label() }}</small>
    </span>
    <span class="ticket-main">
        <strong>{{ $ticket->subject }}</strong>
        <span><x-icon :name="$ticket->category->icon()" /> {{ $ticket->category->label() }}, aberto por {{ $ticket->requester->name }}</span>
    </span>
    <span class="ticket-meta">
        <span class="status status-{{ $ticket->status->value }}">{{ $ticket->status->label() }}</span>
        @if ($ticket->assignee)
            <span class="assignee"><x-avatar :user="$ticket->assignee" class="avatar avatar-sm" /> {{ $ticket->assignee->name }}</span>
        @else
            <span class="assignee muted"><x-icon name="user-x" /> Sem responsável</span>
        @endif
    </span>
    <span class="ticket-sla">
        <span class="sla sla-{{ $ticket->slaState() }}"><x-icon :name="$ticket->slaState() === 'breached' ? 'alert' : ($ticket->resolved_at ? 'check' : 'clock')" /> {{ $ticket->slaLabel() }}</span>
        <span class="meter meter-{{ $ticket->slaState() }}" aria-hidden="true"><i style="--fill: {{ round($ticket->slaProgress() * 100) }}%"></i></span>
    </span>
</a>
