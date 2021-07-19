<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdatePost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    public function index(){
        $posts = Post::latest()->paginate();
        //ou orderBy('id', 'Desc')
        
        
        $usuarios = array();
        foreach($posts as $post){
            $usuario = User::with('post')->findOrFail($post->id_usuario);
            array_push($usuarios, $usuario->name);
        }

        return view('admin.posts.index', compact('posts', 'usuarios'));
        // ou ['posts'=>$posts,]
    }

    public function create(){

        return view('admin.posts.create');
    }

    public function store(StoreUpdatePost $request){
        $data = $request->all();
        $data['id_usuario'] = Auth::user()->id;
        if($request->image->isValid()){
            $nameFile = Str::of($request->title)->slug('-').'.'.$request->image->getClientOriginalExtension(); 
            $image = $request->image->storeAs('posts', $nameFile);
            $data['image'] = $image;
        }
        Post::create($data);

        return redirect()->route('posts.index')->with('Chamado criado com sucesso');
        //dd($request->all());
    }

    public function show($id){
        //$post = Post::where('id', $id)->first();
        if(!$post = Post::find($id)){
            return redirect()->route('posts.index');
        }
        
        return view('admin.posts.show', compact('post'));
    }

    public function destroy($id){
        
        if(!$post = Post::find($id)){
            return redirect()->route('posts.index');
        }
        if(Storage::exists($post->image)){
            Storage::delete($post->image);
        }
        $post->delete();
        
        return redirect()->route('posts.index')->with('message', 'Post Deletado com Sucesso');
    }

    public function edit($id){
        if(!$post = Post::find($id)){
            return redirect()->route('posts.index');
        }
        
        return view('admin.posts.edit', compact('post'));
    }

    public function update(StoreUpdatePost $request, $id){
        if(!$post = Post::find($id)){
            return redirect()->route('posts.index');
        }

        $data = $request->all();

        if($request->image && $request->image->isValid()){

            if(Storage::exists($post->image)){
                Storage::delete($post->image);
            }
            $nameFile = Str::of($request->title)->slug('-').'.'.$request->image->getClientOriginalExtension(); 
            $image = $request->image->storeAs('posts', $nameFile);
            $data['image'] = $image;
        }
        
        $post->update($data);

        return redirect()->route('posts.index')->with('message', 'Post editado com sucesso');
    }

    public function search(Request $request){
        $filters = $request->except('_token');

        $posts = Post::where('title', 'LIKE', "%{$request->search}%")->orWhere('content', 'LIKE', "%{$request->search}%")->paginate();
        //toSql
        //dd($request);
        
        return view('admin.posts.index', compact('posts', 'filters'));
    }
}
