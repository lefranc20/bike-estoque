<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use Illuminate\Http\Request;

class MovimentacaoEstoqueController extends Controller
{
    public function index()
    {
        return MovimentacaoEstoque::with(['produto', 'usuario'])->latest()->get();
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo' => 'required|in:entrada,saida,ajuste',
            'quantidade' => 'required|integer|min:1',
            'motivo' => 'nullable|string',
        ]);

        $produto = Produto::findOrFail($dados['produto_id']);
        $quantidadeAnterior = $produto->quantidade;

        if ($dados['tipo'] === 'entrada') {
            $produto->quantidade += $dados['quantidade'];
        } elseif ($dados['tipo'] === 'saida') {
            if ($produto->quantidade < $dados['quantidade']) {
                return response()->json(['message' => 'Estoque insuficiente'], 422);
            }
            $produto->quantidade -= $dados['quantidade'];
        } else {
            // ajuste
            $produto->quantidade = $dados['quantidade'];
        }

        $produto->save();

        $movimentacao = MovimentacaoEstoque::create([
            'produto_id' => $produto->id,
            'tipo' => $dados['tipo'],
            'quantidade' => $dados['quantidade'],
            'quantidade_anterior' => $quantidadeAnterior,
            'quantidade_nova' => $produto->quantidade,
            'motivo' => $dados['motivo'] ?? null,
            'user_id' => 1, // depois vamos melhorar isso com login
        ]);

        return response()->json($movimentacao->load('produto'), 201);
    }
}