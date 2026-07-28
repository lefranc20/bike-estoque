<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        return Produto::with('categoria')->get();
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|unique:produtos,codigo',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:0',
            'estoque_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $produto = Produto::create($dados);

        return response()->json($produto->load('categoria'), 201);
    }

    public function show(Produto $produto)
    {
        return $produto->load('categoria');
    }

    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|unique:produtos,codigo,' . $produto->id,
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:0',
            'estoque_minimo' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $produto->update($dados);

        return response()->json($produto->load('categoria'));
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return response()->json(null, 204);
    }
}