@extends('admin.layouts.app')
@section('title', 'Home')
@section('content')

    <a href="{{route('posts.create')}}">Criar novo post</a>
    <hr>
    @if (session('message'))
        <div>
            {{session('message')}}
        </div>
    @endif
    
    <form action="{{route('posts.search')}}" method="post">
        @csrf
        <input type="text" name="search" placeholder="Pesquisar">
        <button type="submit">Buscar</button>
    </form>

    <h1>Posts</h1>
    @foreach($posts as $post)
        
        <p>
            <img src="{{url("storage/{$post->image}")}}" alt="{{$post->title}}" style="max-width:100px;">
            {{$post->title}}  <a href="{{route('posts.show', $post->id)}}">&starf;</a> <a href="{{route('posts.edit', $post->id)}}">Edit</a></p>
        <p>{{$post->content}}</p>
        <br>
    @endforeach
    
    @if (isset($filters))
        <hr>
        {{ $posts->appends($filters)->links() }}    
    @else
        <hr>
        {{ $posts->links() }}
    @endif
 
@endsection