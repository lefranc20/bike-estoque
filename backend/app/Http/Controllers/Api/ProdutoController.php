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

        // Registra a movimentação inicial (entrada)
        if ($produto->quantidade > 0) {
            \App\Models\MovimentacaoEstoque::create([
                'produto_id' => $produto->id,
                'tipo' => 'entrada',
                'quantidade' => $produto->quantidade,
                'quantidade_anterior' => 0,
                'quantidade_nova' => $produto->quantidade,
                'motivo' => 'Cadastro inicial do produto',
                'user_id' => 1,
            ]);
        }

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

        $quantidadeAnterior = $produto->quantidade;

        $produto->update($dados);

        // Se a quantidade mudou, registra a movimentação
        if ($quantidadeAnterior != $produto->quantidade) {
            $tipo = $produto->quantidade > $quantidadeAnterior ? 'entrada' : 'saida';
            $quantidadeMovimentada = abs($produto->quantidade - $quantidadeAnterior);

            \App\Models\MovimentacaoEstoque::create([
                'produto_id' => $produto->id,
                'tipo' => $tipo,
                'quantidade' => $quantidadeMovimentada,
                'quantidade_anterior' => $quantidadeAnterior,
                'quantidade_nova' => $produto->quantidade,
                'motivo' => 'Alteração manual da quantidade',
                'user_id' => 1,
            ]);
        }

        return response()->json($produto->load('categoria'));
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return response()->json(null, 204);
    }
}