@extends('admin.layouts.app')

@section('title', 'Lista de Chamados')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold text-white mb-1">Painel de Chamados</h2>
        <p class="text-slate-400 text-sm">Gerencie e responda as solicitações de suporte</p>
    </div>

    <div class="mt-4 md:mt-0 flex gap-4 w-full md:w-auto">
        <form action="{{ route('posts.search') }}" method="post" class="relative flex-1 md:w-64">
            @csrf
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-slate-400"></i>
            </div>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Pesquisar chamado..." required
                   class="w-full pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-white font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder-slate-500">
        </form>
        
        <a href="{{ route('posts.create') }}" class="flex-shrink-0 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-primary/30 hover:opacity-90 transform transition hover:-translate-y-0.5">
            <i class="fas fa-plus mr-2"></i> Novo
        </a>
    </div>
</div>

@if (session('message'))
<div class="mb-6 p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-start fade-in">
    <div class="flex-shrink-0">
        <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
    </div>
    <div class="ml-3">
        <p class="text-sm font-medium text-emerald-400">{{ session('message') }}</p>
    </div>
</div>
@endif

@if (session('error'))
<div class="mb-6 p-4 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-start fade-in">
    <div class="flex-shrink-0">
        <i class="fas fa-exclamation-circle text-rose-400 mt-0.5"></i>
    </div>
    <div class="ml-3">
        <p class="text-sm font-medium text-rose-400">{{ session('error') }}</p>
    </div>
</div>
@endif

<div class="glass rounded-xl border border-slate-700/50 overflow-hidden shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-800/80 border-b border-slate-700/50 text-xs uppercase tracking-wider text-slate-400 font-semibold">
                    <th class="px-6 py-4 rounded-tl-xl w-24">Ticket</th>
                    <th class="px-6 py-4">Assunto</th>
                    <th class="px-6 py-4">Requisitante</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right rounded-tr-xl">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50 text-sm">
                @forelse ($posts as $post)
                <tr class="hover:bg-slate-700/30 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-medium text-primary bg-primary/10 px-2 py-1 rounded-md">#{{ str_pad($post->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($post->image)
                            <div class="h-10 w-10 flex-shrink-0 rounded-lg overflow-hidden bg-slate-800 border border-slate-600 mr-3">
                                <img src="{{ route('image.displayImage', ['filename' => basename($post->image)]) }}" alt="Anexo" class="h-full w-full object-cover">
                            </div>
                            @else
                            <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center mr-3 text-slate-500">
                                <i class="fas fa-image"></i>
                            </div>
                            @endif
                            <div>
                                <h3 class="font-medium text-white group-hover:text-primary transition-colors line-clamp-1">{{ $post->title }}</h3>
                                <p class="text-slate-400 text-xs mt-0.5 line-clamp-1 truncate w-48 lg:w-96">{{ $post->content }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-user-circle text-slate-500 mr-2"></i>
                            {{ $post->user->name ?? 'Sistema' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Pendente
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2 opacity-80 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('posts.show', $post->id) }}" class="p-2 text-primary hover:bg-primary/20 rounded-lg transition-colors tooltip" title="Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('posts.edit', $post->id) }}" class="p-2 text-emerald-400 hover:bg-emerald-400/20 rounded-lg transition-colors tooltip" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="inline" onsubmit="return confirm('Deseja realmente deletar este chamado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-400 hover:bg-rose-400/20 rounded-lg transition-colors tooltip" title="Excluir">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="inline-flex flex-col items-center justify-center">
                            <i class="fas fa-inbox text-5xl text-slate-600 mb-4"></i>
                            <h3 class="text-lg font-medium text-white mb-1">Nenhum chamado encontrado</h3>
                            <p class="text-slate-400 mb-4 text-sm">Os chamados criados aparecerão aqui.</p>
                            <a href="{{ route('posts.create') }}" class="text-primary hover:text-white transition-colors text-sm font-medium">Criar seu primeiro chamado &rarr;</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex justify-end">
    @if (isset($filters))
        {{ $posts->appends($filters)->links('pagination::tailwind') }}
    @else
        {{ $posts->links('pagination::tailwind') }}
    @endif
</div>

@endsection