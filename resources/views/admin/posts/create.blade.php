@extends('admin.layouts.app')
@section('title', 'Cadastro de Novo Chamado')
@section('content')

    <h1 class="tex-center text-3x1 uppercase font-black my-4">Cadastrar novo post</h1>

    <div class="w-11/12 p-12 bg-white sm:w-8/12 md:w-1/2 1g:w-5/12 mx-auto">
        <form action="{{route('posts.store')}}" method="post" enctype="multipart/form-data">
            @include('admin.posts._partials.form');
        </form>
    </div>
    
@endsection