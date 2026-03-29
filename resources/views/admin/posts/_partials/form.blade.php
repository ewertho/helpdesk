@if($errors->any())
    <div class="mb-6 p-4 rounded-lg bg-rose-500/10 border border-rose-500/20">
        <ul class="list-disc list-inside text-sm text-rose-400">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-slate-300 mb-1">Assunto do Chamado</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-heading text-slate-500"></i>
            </div>
            <input type="text" name="title" id="title" placeholder="Descreva brevemente o problema" value="{{ $post->title ?? old('title') }}" 
                   class="w-full pl-10 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-lg text-white font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder-slate-500">
        </div>
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-slate-300 mb-1">Detalhes do Requisito</label>
        <textarea name="content" id="content" placeholder="Explique os detalhes do chamado, passos para reproduzir, etc." rows="5" 
                  class="w-full p-4 bg-slate-800/50 border border-slate-700 rounded-lg text-white resize-none font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder-slate-500">{{ $post->content ?? old('content') }}</textarea>
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-slate-300 mb-1">Anexar Captura de Tela (Opcional)</label>
        <div class="relative">
            <input type="file" name="image" id="image" 
                   class="w-full text-slate-300 bg-slate-800/50 border border-slate-700 rounded-lg p-2 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-indigo-500 transition-all cursor-pointer">
        </div>
    </div>
    
    <div class="pt-4 flex justify-end">
        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white font-medium rounded-lg shadow-lg shadow-primary/30 hover:opacity-90 transform transition hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary focus:ring-offset-slate-900 w-full md:w-auto">
            <i class="fas fa-paper-plane mr-2"></i> {{ isset($post) ? 'Atualizar Chamado' : 'Enviar Solicitação' }}
        </button>
    </div>
</div>