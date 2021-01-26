<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdatePost;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(){
        $posts = Post::latest()->paginate();
        //ou orderBy('id', 'Desc')

        return view('admin.posts.index', compact('posts'));
        // ou ['posts'=>$posts,]
    }

    public function create(){

        return view('admin.posts.create');
    }

    public function store(StoreUpdatePost $request){
        Post::create($request->all());

        return redirect()->route('posts.index');
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
        
        $post->update($request->all());

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
