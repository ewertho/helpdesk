@extends('admin.layouts.app')
@section('title', 'Editar Posts')
@section('content')

    <h1>Editar o Post <strong>{{$post->title}}</strong></h1>
    
    <form action="{{route('posts.update', $post->id)}}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        @include('admin.posts._partials.form');
    </form>

@endsection