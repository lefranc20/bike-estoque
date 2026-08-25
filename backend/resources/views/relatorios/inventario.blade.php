<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Inventário</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitulo { color: #64748b; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 9px; letter-spacing: 0.05em; }
        td.numero, th.numero { text-align: right; }
        tfoot td { font-weight: bold; border-top: 2px solid #1e293b; border-bottom: none; }
        .abaixo-minimo { color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Relatório de Inventário</h1>
    <p class="subtitulo">Gerado em {{ $geradoEm->format('d/m/Y H:i') }} &middot; {{ $produtos->count() }} produto(s)</p>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th class="numero">Preço</th>
                <th class="numero">Quantidade</th>
                <th class="numero">Estoque mínimo</th>
                <th class="numero">Valor em estoque</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produtos as $produto)
                <tr>
                    <td>{{ $produto->codigo }}</td>
                    <td class="{{ $produto->esta_abaixo_do_minimo ? 'abaixo-minimo' : '' }}">{{ $produto->nome }}</td>
                    <td>{{ $produto->categoria->nome ?? '—' }}</td>
                    <td class="numero">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td class="numero">{{ $produto->quantidade }}</td>
                    <td class="numero">{{ $produto->estoque_minimo }}</td>
                    <td class="numero">R$ {{ number_format($produto->preco * $produto->quantidade, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">Valor total em estoque</td>
                <td class="numero">R$ {{ number_format($valorTotal, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
