@extends('admin.layouts.app')

@section('title', 'Detalhes do Chamado')

@section('content')

<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
    <div>
        <a href="{{ route('posts.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm mb-4 inline-block"><i class="fas fa-arrow-left mr-2"></i>Voltar para a Lista</a>
        <h2 class="text-3xl font-bold text-white mb-1">Chamado #{{ str_pad($post->id, 4, '0', STR_PAD_LEFT) }}</h2>
        <p class="text-slate-400 text-sm">Aberto por <span class="text-slate-300 font-medium">{{ $post->user->name ?? 'Sistema' }}</span> em {{ $post->created_at->format('d/m/Y H:i') }}</p>
    </div>
    
    <div class="mt-4 md:mt-0 flex space-x-3">
        <a href="{{ route('posts.edit', $post->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium rounded-lg hover:bg-emerald-500/20 transition-colors shadow-sm">
            <i class="fas fa-edit mr-2"></i> Editar
        </a>
        <form action="{{ route('posts.destroy', $post->id) }}" method="post" class="inline" onsubmit="return confirm('Tem certeza que deseja apagar este chamado permanentemente?');">
            @csrf
            @method('delete')
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-rose-500/10 text-rose-400 border border-rose-500/20 font-medium rounded-lg hover:bg-rose-500/20 transition-colors shadow-sm">
                <i class="fas fa-trash-alt mr-2"></i> Excluir
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 fade-in">
    <!-- Main Content Col -->
    <div class="lg:col-span-2 space-y-6">
        <div class="glass rounded-xl border border-slate-700/50 shadow-2xl overflow-hidden p-6 md:p-8">
            <h3 class="text-xl font-semibold mb-6 text-primary border-b border-primary/20 pb-4">{{ $post->title }}</h3>
            <div class="bg-slate-800/50 p-6 rounded-lg border border-slate-700/50 whitespace-pre-wrap text-slate-300 text-sm leading-relaxed">{{ $post->content }}</div>
        </div>

        @if($post->image)
        <div class="glass rounded-xl border border-slate-700/50 shadow-2xl overflow-hidden p-6 md:p-8">
            <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-700/50 pb-3"><i class="fas fa-paperclip mr-2"></i> Arquivo Anexo</h4>
            <div class="bg-slate-800/80 p-3 rounded-lg border border-slate-700/50 inline-block w-full text-center">
                <img src="{{ route('image.displayImage', ['filename' => basename($post->image)]) }}" alt="Anexo de {{ $post->title }}" class="mx-auto rounded-md shadow-lg object-contain max-h-[500px]">
            </div>
        </div>
        @endif
    </div>
    
    <!-- Sidebar Col -->
    <div class="space-y-6">
        <div class="glass rounded-xl border border-slate-700/50 shadow-xl overflow-hidden p-6">
            <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-5 border-b border-slate-700/50 pb-3">Informações do Chamado</h4>
            
            <div class="space-y-5">
                <div>
                    <p class="text-xs text-slate-500 mb-1.5 uppercase font-medium tracking-wider">Status Atual</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        <i class="fas fa-clock mr-1.5"></i> Pendente
                    </span>
                </div>
                
                <div>
                    <p class="text-xs text-slate-500 mb-1.5 uppercase font-medium tracking-wider">Requisitante</p>
                    <div class="flex items-center text-white text-sm bg-slate-800/50 p-2 rounded-lg border border-slate-700/50">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-xs mr-3 shadow-inner">
                            {{ substr($post->user->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="font-medium">{{ $post->user->name ?? 'Sistema' }}</span>
                    </div>
                </div>
                
                <div>
                    <p class="text-xs text-slate-500 mb-1.5 uppercase font-medium tracking-wider">Data de Abertura</p>
                    <p class="text-slate-300 text-sm flex items-center"><i class="far fa-calendar-alt text-primary mr-2"></i>{{ $post->created_at->format('d/m/Y \à\s H:i') }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-1.5 uppercase font-medium tracking-wider">Última Atualização</p>
                    <p class="text-slate-300 text-sm flex items-center"><i class="fas fa-history text-secondary mr-2"></i>{{ $post->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection