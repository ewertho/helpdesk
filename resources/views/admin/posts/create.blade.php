<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Cadastrar novo post</h1>
    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    @endif
    <form action="{{route('posts.store')}}" method="post">
        @csrf
        <input type="text" name="title" id="title" placeholder="Titulo" value="{{old('title')}}">
        <textarea name="content" id="content" cols="30" rows="10">{{old('content')}}</textarea>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>