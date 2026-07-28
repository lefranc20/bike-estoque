<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\MovimentacaoEstoque;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProdutos = Produto::count();
        $totalCategorias = Categoria::count();
        $valorTotalEstoque = Produto::selectRaw('SUM(preco * quantidade) as total')->value('total') ?? 0;
        $produtosEstoqueBaixo = Produto::whereColumn('quantidade', '<=', 'estoque_minimo')->with('categoria')->get();
        $ultimasMovimentacoes = MovimentacaoEstoque::with('produto')->latest()->take(5)->get();

        return response()->json([
            'total_produtos' => $totalProdutos,
            'total_categorias' => $totalCategorias,
            'valor_total_estoque' => $valorTotalEstoque,
            'produtos_estoque_baixo' => $produtosEstoqueBaixo,
            'ultimas_movimentacoes' => $ultimasMovimentacoes,
        ]);
    }
}