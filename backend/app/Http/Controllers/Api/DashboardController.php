<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\MovimentacaoEstoque;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProdutos = Produto::count();
        $totalCategorias = Categoria::count();
        $valorTotalEstoque = Produto::selectRaw('SUM(preco * quantidade) as total')->value('total') ?? 0;
        $produtosAbaixoDoMinimo = Produto::whereColumn('quantidade', '<', 'estoque_minimo')->with('categoria')->get();
        $produtosEstoqueBaixo = Produto::whereColumn('quantidade', '<=', 'estoque_minimo')->with('categoria')->get();
        $ultimasMovimentacoes = MovimentacaoEstoque::with('produto')->latest()->take(5)->get();

        return response()->json([
            'total_produtos' => $totalProdutos,
            'total_categorias' => $totalCategorias,
            'valor_total_estoque' => $valorTotalEstoque,
            'produtos_abaixo_do_minimo' => $produtosAbaixoDoMinimo,
            'produtos_estoque_baixo' => $produtosEstoqueBaixo,
            'ultimas_movimentacoes' => $ultimasMovimentacoes,
        ]);
    }

    public function movimentacoesPorPeriodo(Request $request)
    {
        $dados = $request->validate([
            'granularidade' => 'nullable|in:dia,mes,ano',
            'quantidade' => 'nullable|integer|min:1|max:90',
        ]);

        $granularidade = $dados['granularidade'] ?? 'dia';
        $limites = ['dia' => 90, 'mes' => 36, 'ano' => 10];
        $quantidade = min($dados['quantidade'] ?? 14, $limites[$granularidade]);

        $inicio = match ($granularidade) {
            'mes' => now()->subMonths($quantidade - 1)->startOfMonth(),
            'ano' => now()->subYears($quantidade - 1)->startOfYear(),
            default => now()->subDays($quantidade - 1)->startOfDay(),
        };

        $chave = fn ($momento) => match ($granularidade) {
            'mes' => $momento->format('Y-m'),
            'ano' => $momento->format('Y'),
            default => $momento->toDateString(),
        };

        $buckets = [];
        foreach (range(0, $quantidade - 1) as $offset) {
            $momento = match ($granularidade) {
                'mes' => $inicio->copy()->addMonths($offset),
                'ano' => $inicio->copy()->addYears($offset),
                default => $inicio->copy()->addDays($offset),
            };

            $buckets[$chave($momento)] = ['data' => $chave($momento), 'entradas' => 0, 'saidas' => 0];
        }

        MovimentacaoEstoque::select('created_at', 'tipo', 'quantidade')
            ->where('created_at', '>=', $inicio)
            ->whereIn('tipo', ['entrada', 'saida'])
            ->chunk(500, function ($movimentacoes) use (&$buckets, $chave) {
                foreach ($movimentacoes as $movimentacao) {
                    $bucket = $chave($movimentacao->created_at);

                    if (! isset($buckets[$bucket])) {
                        continue;
                    }

                    $campo = $movimentacao->tipo === 'entrada' ? 'entradas' : 'saidas';
                    $buckets[$bucket][$campo] += $movimentacao->quantidade;
                }
            });

        return response()->json(array_values($buckets));
    }
}