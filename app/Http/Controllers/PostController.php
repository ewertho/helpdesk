<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdatePost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate();
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(StoreUpdatePost $request)
    {
        $data = $request->all();
        $data['id_usuario'] = Auth::id();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $nameFile = Str::of($request->title)->slug('-') . '.' . $request->image->getClientOriginalExtension(); 
            $image = $request->file('image')->storeAs('posts', $nameFile, 'public');
            $data['image'] = $image;
        }

        Post::create($data);

        return redirect()->route('posts.index')->with('message', 'Chamado criado com sucesso');
    }

    public function show($id)
    {
        if (!$post = Post::find($id)) {
            return redirect()->route('posts.index')->with('error', 'Chamado não encontrado');
        }
        
        return view('admin.posts.show', compact('post'));
    }

    public function destroy($id)
    {
        if (!$post = Post::find($id)) {
            return redirect()->route('posts.index')->with('error', 'Chamado não encontrado');
        }

        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();
        
        return redirect()->route('posts.index')->with('message', 'Chamado deletado com sucesso');
    }

    public function edit($id)
    {
        if (!$post = Post::find($id)) {
            return redirect()->route('posts.index')->with('error', 'Chamado não encontrado');
        }
        
        return view('admin.posts.edit', compact('post'));
    }

    public function update(StoreUpdatePost $request, $id)
    {
        if (!$post = Post::find($id)) {
            return redirect()->route('posts.index')->with('error', 'Chamado não encontrado');
        }

        $data = $request->all();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $nameFile = Str::of($request->title)->slug('-') . '.' . $request->image->getClientOriginalExtension(); 
            $image = $request->file('image')->storeAs('posts', $nameFile, 'public');
            $data['image'] = $image;
        }
        
        $post->update($data);

        return redirect()->route('posts.index')->with('message', 'Chamado editado com sucesso');
    }

    public function search(Request $request)
    {
        $filters = $request->except('_token');

        $posts = Post::with('user')
            ->where('title', 'LIKE', "%{$request->search}%")
            ->orWhere('content', 'LIKE', "%{$request->search}%")
            ->paginate();
        
        return view('admin.posts.index', compact('posts', 'filters'));
    }
}
