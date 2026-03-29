@extends('admin.layouts.app')

@section('title', 'Novo Chamado')

@section('content')

<div class="mb-8">
    <a href="{{ route('posts.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm mb-4 inline-block"><i class="fas fa-arrow-left mr-2"></i>Voltar para a Lista</a>
    <h2 class="text-3xl font-bold text-white mb-1">Abrir Novo Chamado</h2>
    <p class="text-slate-400 text-sm">Preencha os dados abaixo para relatar seu problema</p>
</div>

<div class="glass rounded-xl border border-slate-700/50 shadow-2xl p-6 md:p-8 max-w-3xl fade-in">
    <form action="{{ route('posts.store') }}" method="post" enctype="multipart/form-data">
        @include('admin.posts._partials.form')
    </form>
</div>

@endsection