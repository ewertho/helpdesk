@extends('admin.layouts.app')

@section('title', 'Listagem dos Posts')

@section('content')

<h1 class="text-center text-3xl text-white uppercase my-4">Listagem de Chamados</h1>

<div class="flex bg-white justify-between p-4">
    <a href="{{ route('posts.create') }}" class="my-4 uppercase px-8 p-3 rounded bg-green-600 text-blue-50 max-w-max shadow-sm hover:shadow-lg">Criar Chamado</a>
    <form action="{{ route('posts.search') }}" method="post" class="">
        @csrf
        <div class="max-w-sm my-4 p-1 pr-0 items-center justify-between">
            <input type="text" name="search" placeholder="Filtrar:" class="flex-1 appearance-none rounded shadow p-3 text-grey-dark mr-2 focus:outline-none">
            <button type="submit" class="uppercase p-3 rounded bg-blue-500 text-blue-50 max-w-max shadow-sm hover:shadow-lg"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>
@if (session('message'))
<div class="flex justify-center items-center m-1 font-medium py-1 px-2 bg-white rounded-md text-green-700 bg-green-100 border border-green-300 ">
    {{ session('message') }}
</div>
@endif



<table class="min-w-full bg-white">
    <thead>
        <tr>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">ID</th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Imagem</th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Nome</th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Conteudo</th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">Usuario</th>
            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider"></th>
        </tr>
    </thead>
    <tbody>

        @foreach ($posts as $key => $post)

        <tr>
            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5">
                #{{ $post->id }}
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                <img src="{{ url("storage/{$post->image}") }}" alt="{{ $post->title }}" class="w-16">
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5">{{ $post->title }}</td>
            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5">{{ $post->content }}</td>
            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5">{{ $usuarios[$key] }}</td>
            <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5 text-right">
                <a href="{{ route('posts.show', $post->id) }}" class="px-5 py-2 border-red-500 border text-red-500 rounded transition duration-300 hover:bg-red-700 hover:text-white focus:outline-none">Deletar <i class="fas fa-trash"></i></a>
                <a href="{{ route('posts.edit', $post->id) }}" class="px-5 py-2 border-green-500 border text-green-500 rounded transition duration-300 hover:bg-green-700 hover:text-white focus:outline-none">Editar <i class="fas fa-pencil-alt"></i></a>
            </td>
        </tr>
        @endforeach

    </tbody>
</table>



<div class="my-4">
    @if (isset($filters))
    {{ $posts->appends($filters)->links() }}
    @else
    {{ $posts->links() }}
    @endif
</div>

@endsection