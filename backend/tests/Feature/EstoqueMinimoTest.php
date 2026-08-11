<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstoqueMinimoTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_api_indica_quando_o_produto_esta_abaixo_do_estoque_minimo(): void
    {
        $categoria = Categoria::create(['nome' => 'Acessórios']);

        Produto::create([
            'nome' => 'Buzina',
            'codigo' => 'BUZ-001',
            'descricao' => 'Buzina elétrica',
            'preco' => 35.50,
            'quantidade' => 2,
            'estoque_minimo' => 5,
            'categoria_id' => $categoria->id,
        ]);

        $response = $this->getJson('/api/produtos');

        $response->assertOk();
        $response->assertJsonFragment(['esta_abaixo_do_minimo' => true]);
    }
}
