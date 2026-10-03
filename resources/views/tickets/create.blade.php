@extends('layout')
@section('title', 'Abrir chamado')
@section('page-class', 'page-narrow')

@section('content')
    <a class="back" href="{{ route('tickets.index') }}">Chamados</a>
    <h1>Abrir chamado</h1>

    <form class="sheet form" method="post" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
        @csrf
        <label>Assunto
            <input name="subject" value="{{ old('subject') }}" required minlength="5" maxlength="160" autofocus>
            <small>Uma frase que diga o que está acontecendo. Ex.: “Impressora do 2º andar não imprime”.</small>
        </label>

        <label>O que aconteceu?
            <textarea name="description" rows="7" required minlength="10" maxlength="5000">{{ old('description') }}</textarea>
            <small>Conte o que você tentou fazer, o que apareceu na tela e desde quando acontece.</small>
        </label>

        <div class="form-row">
            <label>Categoria
                <select name="category" required>
                    @foreach (App\Enums\Category::cases() as $category)
                        <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                    @endforeach
                </select>
            </label>

            <label>Urgência
                <select name="priority" required>
                    @foreach (App\Enums\Priority::cases() as $priority)
                        <option value="{{ $priority->value }}" @selected(old('priority', 'normal') === $priority->value)>
                            {{ $priority->label() }}: solução em até {{ $priority->slaHours() }}h
                        </option>
                    @endforeach
                </select>
            </label>
        </div>

        <label>Anexo, se ajudar a explicar
            <input type="file" name="attachment" accept=".png,.jpg,.jpeg,.webp,.pdf,.txt,.log">
            <small>Imagem, PDF ou texto de até 5 MB.</small>
        </label>

        <div class="form-actions">
            <a href="{{ route('tickets.index') }}">Cancelar</a>
            <button class="btn"><x-icon name="send" /> Abrir chamado</button>
        </div>
    </form>
@endsection
