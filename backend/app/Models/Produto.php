<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'preco',
        'quantidade',
        'estoque_minimo',
        'categoria_id',
    ];

    protected $appends = ['esta_abaixo_do_minimo', 'esta_em_estoque_critico'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoEstoque::class);
    }

    public function getEstaAbaixoDoMinimoAttribute(): bool
    {
        return (int) $this->quantidade < (int) $this->estoque_minimo;
    }

    public function getEstaEmEstoqueCriticoAttribute(): bool
    {
        return (int) $this->quantidade <= (int) $this->estoque_minimo / 2;
    }
}