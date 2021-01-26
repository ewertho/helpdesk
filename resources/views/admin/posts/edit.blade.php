<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Editar o Post <strong>{{$post->title}}</strong></h1>
    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    @endif
    <form action="{{route('posts.update', $post->id)}}" method="post">
        @csrf
        @method('put')
        <input type="text" name="title" id="title" placeholder="Titulo" value="{{$post->title}}">
        <textarea name="content" id="content" cols="30" rows="10">{{$post->content}}</textarea>
        <button type="submit">Enviar</button>
    </form>
    <button><a href="{{route('posts.index')}}">Voltar</a></button>
</body>
</html>