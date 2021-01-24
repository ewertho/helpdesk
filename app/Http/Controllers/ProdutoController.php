<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(){
        return view('produtos.index');
    }
    public function create(){
        return view('produtos.create');
    }
    public function show($nome, $valor=null){
        if($valor){
            return "o produto $nome com valor de R$ $valor";
        }else{
            return view('produtos.show', ['nome'=>$nome]);
        }
    }
}
