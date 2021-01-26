<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
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
</body>
</html>