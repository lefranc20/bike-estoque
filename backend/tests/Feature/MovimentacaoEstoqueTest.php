<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovimentacaoEstoqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_registrar_uma_entrada_e_uma_saida_sem_autenticacao(): void
    {
        $categoria = Categoria::create(['nome' => 'Acessórios']);
        $produto = Produto::create([
            'nome' => 'Freio de disco',
            'codigo' => 'FREIO-001',
            'descricao' => 'Freio para bicicleta',
            'preco' => 89.90,
            'quantidade' => 10,
            'estoque_minimo' => 2,
            'categoria_id' => $categoria->id,
        ]);

        $entradaResponse = $this->postJson('/api/movimentacoes', [
            'produto_id' => $produto->id,
            'tipo' => 'entrada',
            'quantidade' => 3,
            'motivo' => 'Reposição',
        ]);

        $entradaResponse->assertCreated();
        $this->assertSame(13, $produto->fresh()->quantidade);

        $saidaResponse = $this->postJson('/api/movimentacoes', [
            'produto_id' => $produto->id,
            'tipo' => 'saida',
            'quantidade' => 4,
            'motivo' => 'Venda',
        ]);

        $saidaResponse->assertCreated();
        $this->assertSame(9, $produto->fresh()->quantidade);
    }
}
