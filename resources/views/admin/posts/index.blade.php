<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
        <p>{{$post->title}}  <a href="{{route('posts.show', $post->id)}}">&starf;</a> <a href="{{route('posts.edit', $post->id)}}">Edit</a></p>
        <p>{{$post->content}}</p>
        <br>
    @endforeach
    <hr>
    @if (isset($filters))
        {{ $posts->appends($filters)->links() }}    
    @else
        {{ $posts->links() }}
    @endif
    
</body>
</html>