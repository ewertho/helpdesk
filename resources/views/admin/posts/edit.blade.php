@extends('admin.layouts.app')

@section('title', 'Editar Chamado')

@section('content')

<div class="mb-8">
    <a href="{{ route('posts.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm mb-4 inline-block"><i class="fas fa-arrow-left mr-2"></i>Voltar para a Lista</a>
    <h2 class="text-3xl font-bold text-white mb-1">Editar Chamado #{{ str_pad($post->id, 4, '0', STR_PAD_LEFT) }}</h2>
    <p class="text-slate-400 text-sm">Atualize os dados desta solicitação de suporte</p>
</div>

<div class="glass rounded-xl border border-slate-700/50 shadow-2xl p-6 md:p-8 max-w-3xl fade-in">
    <form action="{{ route('posts.update', $post->id) }}" method="post" enctype="multipart/form-data">
        @method('put')
        @include('admin.posts._partials.form')
    </form>
</div>

@endsection