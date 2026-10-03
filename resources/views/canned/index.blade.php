@extends('layout')
@section('title', 'Respostas prontas')
@section('page-class', 'page-narrow')

@section('content')
    <header class="page-head">
        <h1>Respostas prontas</h1>
        <p class="lead">Textos que a equipe insere com um clique ao responder um chamado.</p>
    </header>

    @forelse ($replies as $reply)
        <article class="sheet canned-item">
            <header>
                <h2><x-icon name="zap" /> {{ $reply->title }}</h2>
                <form method="post" action="{{ route('canned.destroy', $reply) }}">
                    @csrf @method('delete')
                    <button class="icon-btn" aria-label="Remover {{ $reply->title }}" title="Remover"><x-icon name="trash" /></button>
                </form>
            </header>
            <p class="body">{{ $reply->body }}</p>
        </article>
    @empty
        <div class="sheet empty"><x-icon name="zap" /><h2>Nenhuma resposta pronta ainda.</h2><p>Crie a primeira abaixo para responder mais rápido.</p></div>
    @endforelse

    <form class="sheet form" method="post" action="{{ route('canned.store') }}">
        @csrf
        <h2>Nova resposta pronta</h2>
        <label>Título
            <input name="title" value="{{ old('title') }}" required maxlength="80">
            <small>Como ela aparece no botão. Ex.: “Pedir mais detalhes”.</small>
        </label>
        <label>Texto
            <textarea name="body" rows="5" required maxlength="5000">{{ old('body') }}</textarea>
        </label>
        <button class="btn"><x-icon name="plus" /> Salvar resposta pronta</button>
    </form>
@endsection
