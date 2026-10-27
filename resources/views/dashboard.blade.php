@extends('layout')
@section('title', 'Painel')

@php
    $minutes = fn (?float $value) => $value === null ? 'sem dados' : ($value >= 60 ? round($value / 60, 1).' h' : round($value).' min');
    $maxStatus = max(1, $byStatus->max(fn ($row) => $row[1]));
    $maxCategory = max(1, $byCategory->max(fn ($row) => $row[1]));
@endphp

@section('content')
    <header class="page-head">
        <h1>Painel</h1>
        <p class="lead">Como está o atendimento agora.</p>
    </header>

    <div class="kpis">
        <a class="kpi" href="{{ route('tickets.index', ['fila' => 'active']) }}" style="--i: 0">
            <x-icon name="inbox" />
            <b>{{ $active }}</b>
            <span>Em andamento</span>
        </a>
        <a @class(['kpi', 'kpi-alert' => $breached > 0]) href="{{ route('tickets.index', ['fila' => 'breached']) }}" style="--i: 1">
            <x-icon name="alert" />
            <b>{{ $breached }}</b>
            <span>Fora do prazo</span>
        </a>
        <a class="kpi" href="{{ route('tickets.index', ['fila' => 'unassigned']) }}" style="--i: 2">
            <x-icon name="user-x" />
            <b>{{ $unassigned }}</b>
            <span>Sem responsável</span>
        </a>
        <a class="kpi" href="{{ route('tickets.index', ['fila' => 'done']) }}" style="--i: 3">
            <x-icon name="check" />
            <b>{{ $resolvedWeek }}</b>
            <span>Resolvidos em 7 dias</span>
        </a>
    </div>

    <div class="kpis kpis-quality">
        <div class="kpi kpi-ring" style="--i: 4">
            <span class="ring" style="--value: {{ $slaRate ?? 0 }}" role="img" aria-label="{{ $slaRate ?? 0 }}% dos chamados resolvidos no prazo"><b>{{ $slaRate === null ? '—' : $slaRate.'%' }}</b></span>
            <span><strong><x-icon name="target" /> Resolvidos no prazo</strong>Parcela dos chamados resolvidos dentro do SLA.</span>
        </div>
        <div class="kpi kpi-wide" style="--i: 5">
            <x-icon name="timer" />
            <b>{{ $minutes($firstResponse) }}</b>
            <span>Tempo médio até a primeira resposta</span>
        </div>
        <div class="kpi kpi-wide" style="--i: 6">
            <x-icon name="smile" />
            <b>{{ $csat ? number_format($csat, 1, ',') : '—' }}<small> de 5</small></b>
            <span>Satisfação, em {{ $csatCount }} {{ $csatCount === 1 ? 'avaliação' : 'avaliações' }}</span>
        </div>
    </div>

    <div class="panels">
        <section class="sheet">
            <h2>Chamados por status</h2>
            <ul class="bars">
                @foreach ($byStatus as [$status, $total])
                    <li>
                        <span>{{ $status->label() }}</span>
                        <span class="bar status-{{ $status->value }}"><i style="--fill: {{ $total / $maxStatus * 100 }}%"></i></span>
                        <b>{{ $total }}</b>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="sheet">
            <h2>Em andamento por categoria</h2>
            <ul class="bars">
                @foreach ($byCategory as [$category, $total])
                    <li>
                        <span><x-icon :name="$category->icon()" /> {{ $category->label() }}</span>
                        <span class="bar"><i style="--fill: {{ $total / $maxCategory * 100 }}%"></i></span>
                        <b>{{ $total }}</b>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="sheet">
            <h2>Carga por atendente</h2>
            @forelse ($workload as [$agent, $total])
                <p class="load"><x-avatar :user="$agent" /> <span>{{ $agent->name }}</span> <b>{{ $total }}</b></p>
            @empty
                <p class="muted">Nenhum chamado atribuído no momento.</p>
            @endforelse
        </section>
    </div>

    <section>
        <h2 class="section-title">Vencem primeiro</h2>
        @if ($next->isEmpty())
            <div class="sheet empty"><x-icon name="check" /><h2>Fila zerada.</h2><p>Nenhum chamado em andamento.</p></div>
        @else
            <ol class="tickets">
                @foreach ($next as $ticket)
                    <li style="--i: {{ $loop->index }}">@include('tickets._row')</li>
                @endforeach
            </ol>
        @endif
    </section>
@endsection
