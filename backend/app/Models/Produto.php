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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoEstoque::class);
    }
}