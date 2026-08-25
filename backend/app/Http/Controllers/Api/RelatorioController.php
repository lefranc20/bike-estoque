<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovimentacaoEstoque;
use App\Models\Produto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function inventarioCsv()
    {
        $produtos = Produto::with('categoria')->orderBy('nome')->get();

        $callback = function () use ($produtos) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Código', 'Nome', 'Categoria', 'Preço', 'Quantidade', 'Estoque mínimo', 'Valor em estoque'], ';');

            $valorTotal = 0;
            foreach ($produtos as $produto) {
                $valorProduto = $produto->preco * $produto->quantidade;
                $valorTotal += $valorProduto;

                fputcsv($handle, [
                    $produto->codigo,
                    $produto->nome,
                    $produto->categoria->nome ?? '—',
                    number_format($produto->preco, 2, ',', '.'),
                    $produto->quantidade,
                    $produto->estoque_minimo,
                    number_format($valorProduto, 2, ',', '.'),
                ], ';');
            }

            fputcsv($handle, [], ';');
            fputcsv($handle, ['', '', '', '', '', 'Valor total em estoque', number_format($valorTotal, 2, ',', '.')], ';');

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventario.csv"',
        ]);
    }

    public function inventarioPdf()
    {
        $produtos = Produto::with('categoria')->orderBy('nome')->get();
        $valorTotal = $produtos->sum(fn ($produto) => $produto->preco * $produto->quantidade);

        $pdf = Pdf::loadView('relatorios.inventario', [
            'produtos' => $produtos,
            'valorTotal' => $valorTotal,
            'geradoEm' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('inventario.pdf');
    }

    public function movimentacoesCsv(Request $request)
    {
        $movimentacoes = $this->movimentacoesDoPeriodo($request);

        $callback = function () use ($movimentacoes) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Data', 'Produto', 'Tipo', 'Quantidade', 'Anterior', 'Nova', 'Motivo', 'Usuário'], ';');

            foreach ($movimentacoes as $mov) {
                fputcsv($handle, [
                    $mov->created_at->format('d/m/Y H:i'),
                    $mov->produto->nome ?? '—',
                    ucfirst($mov->tipo),
                    $mov->quantidade,
                    $mov->quantidade_anterior,
                    $mov->quantidade_nova,
                    $mov->motivo ?? '—',
                    $mov->usuario->name ?? '—',
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="movimentacoes.csv"',
        ]);
    }

    public function movimentacoesPdf(Request $request)
    {
        $movimentacoes = $this->movimentacoesDoPeriodo($request);

        $pdf = Pdf::loadView('relatorios.movimentacoes', [
            'movimentacoes' => $movimentacoes,
            'dataInicio' => $request->query('data_inicio'),
            'dataFim' => $request->query('data_fim'),
            'geradoEm' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('movimentacoes.pdf');
    }

    private function movimentacoesDoPeriodo(Request $request)
    {
        $dados = $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
        ]);

        $query = MovimentacaoEstoque::with(['produto', 'usuario'])->latest();

        if (! empty($dados['data_inicio'])) {
            $query->whereDate('created_at', '>=', $dados['data_inicio']);
        }

        if (! empty($dados['data_fim'])) {
            $query->whereDate('created_at', '<=', $dados['data_fim']);
        }

        return $query->get();
    }
}
