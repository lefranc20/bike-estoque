<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use Illuminate\Http\Request;

class MovimentacaoEstoqueController extends Controller
{
    public function index(Request $request)
    {
        // 20 é o padrão quando ninguém pede um valor; 200 é o teto máximo que a API aceita
        // entregar numa única resposta, não importa quem peça (protege contra ?per_page=99999).
        // Sem esse teto, ?per_page=99999 forçaria o frontend a renderizar dezenas de milhares
        // de linhas de tabela (milhares de elementos DOM) de uma vez, travando a página —
        // principalmente em celular, onde o navegador pode até matar a aba por falta de memória.
        $porPagina = max(1, min((int) $request->query('per_page', 20), 200));

        $ordenacao = $request->validate([
            'sort' => 'nullable|in:created_at,tipo,quantidade,quantidade_anterior,quantidade_nova,produto',
            'direction' => 'nullable|in:asc,desc',
        ]);

        $coluna = $ordenacao['sort'] ?? 'created_at';
        $direcao = $ordenacao['direction'] ?? 'desc';

        $query = MovimentacaoEstoque::with(['produto', 'usuario']);

        if ($coluna === 'produto') {
            // Ordenar pelo nome do produto exige um join, já que "produto" não é
            // uma coluna da própria tabela de movimentações.
            $query->leftJoin('produtos', 'produtos.id', '=', 'movimentacoes_estoque.produto_id')
                ->select('movimentacoes_estoque.*')
                ->orderBy('produtos.nome', $direcao);
        } else {
            $query->orderBy($coluna, $direcao);
        }

        return $query->paginate($porPagina);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo' => 'required|in:entrada,saida,ajuste',
            'quantidade' => ['required', 'integer', $request->input('tipo') === 'ajuste' ? 'min:0' : 'min:1'],
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
            'user_id' => auth()->id(),
        ]);

        return response()->json($movimentacao->load('produto'), 201);
    }
}