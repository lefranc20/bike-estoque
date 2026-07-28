<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoEstoque extends Model
{
    protected $table = 'movimentacoes_estoque';

    protected $fillable = [
        'produto_id',
        'tipo',
        'quantidade',
        'quantidade_anterior',
        'quantidade_nova',
        'motivo',
        'user_id',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}