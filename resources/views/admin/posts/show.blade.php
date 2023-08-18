@extends('admin.layouts.app')
@section('title', 'Deletar Posts')
@section('content')

<h1 class="text-center text-3xl uppercase text-white my-4">Detalhes do Chamado {{ $post->title }}</h1>

<div class="w-11/12 p-12 bg-white sm:w-8/12 md:w-1/2 lg:w-5/12 mx-auto">
    <ul>
        <li><strong>Título: </strong>{{ $post->title }}</li>
        <li><strong>Conteúdo: </strong>{{ $post->content }}</li>
        <li><strong>Imagem: </strong> <img src="{{ url("storage/{$post->image}") }}" alt="{{ $post->title }}" class="w-auto mx-auto"></li>
    </ul>

    <form action="{{ route('posts.destroy', $post->id) }}" method="post">
        @csrf
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="w-full py-3 mt-6 font-medium tracking-widest text-white uppercase bg-red-500 shadow-lg focus:outline-none hover:bg-red-900 hover:shadow-none">Deletar o Chamado: {{ $post->title }}</button>
    </form>
</div>

@endsection