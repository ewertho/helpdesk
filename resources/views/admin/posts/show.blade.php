@extends('admin.layouts.app')
@section('title', 'Deletar Posts')
@section('content')

    <h1>Destalhe do Post {{$post->title}}</h1>
    <ul>
        <li>Titulo: {{$post->title}}</li>
        <li>Comentario: {{$post->content}}</li>

        <a href="{{route('posts.index')}}">Voltar</a>
    </ul>
    <form action="{{route('posts.destroy', $post->id)}}" method="POST">
        @csrf
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit">Deletar o Post: {{$post->title}}</button>
    </form>

@endsection