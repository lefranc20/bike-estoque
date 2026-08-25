<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Movimentações</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitulo { color: #64748b; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 9px; letter-spacing: 0.05em; }
        td.numero, th.numero { text-align: right; }
        .tipo-entrada { color: #15803d; font-weight: bold; }
        .tipo-saida { color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Relatório de Movimentações de Estoque</h1>
    <p class="subtitulo">
        Gerado em {{ $geradoEm->format('d/m/Y H:i') }} &middot; {{ $movimentacoes->count() }} movimentação(ões)
        @if ($dataInicio || $dataFim)
            &middot; Período:
            {{ $dataInicio ? \Illuminate\Support\Carbon::parse($dataInicio)->format('d/m/Y') : 'início' }}
            até
            {{ $dataFim ? \Illuminate\Support\Carbon::parse($dataFim)->format('d/m/Y') : 'hoje' }}
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th class="numero">Quantidade</th>
                <th class="numero">Anterior</th>
                <th class="numero">Nova</th>
                <th>Motivo</th>
                <th>Usuário</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movimentacoes as $mov)
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $mov->produto->nome ?? '—' }}</td>
                    <td class="{{ $mov->tipo === 'entrada' ? 'tipo-entrada' : ($mov->tipo === 'saida' ? 'tipo-saida' : '') }}">
                        {{ ucfirst($mov->tipo) }}
                    </td>
                    <td class="numero">{{ $mov->quantidade }}</td>
                    <td class="numero">{{ $mov->quantidade_anterior }}</td>
                    <td class="numero">{{ $mov->quantidade_nova }}</td>
                    <td>{{ $mov->motivo ?? '—' }}</td>
                    <td>{{ $mov->usuario->name ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
