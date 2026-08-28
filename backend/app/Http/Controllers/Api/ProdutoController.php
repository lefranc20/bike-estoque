<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProdutoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('admin', only: ['update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        // 20 é o padrão quando ninguém pede um valor; 200 é o teto máximo que a API aceita
        // entregar numa única resposta, não importa quem peça (protege contra ?per_page=99999).
        // Sem esse teto, ?per_page=99999 forçaria o frontend a renderizar dezenas de milhares
        // de linhas de tabela (milhares de elementos DOM) de uma vez, travando a página —
        // principalmente em celular, onde o navegador pode até matar a aba por falta de memória.
        $porPagina = max(1, min((int) $request->query('per_page', 20), 200));

        return Produto::with('categoria')->orderBy('nome')->paginate($porPagina);
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
                'user_id' => auth()->id(),
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
                'user_id' => auth()->id(),
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