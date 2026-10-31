<?php

return [
    'date' => 'O campo :attribute deve ser uma data válida.',
    'exists' => 'O valor selecionado para :attribute é inválido.',
    'in' => 'O valor selecionado para :attribute é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'numeric' => 'O campo :attribute deve ser um número.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório quando :other for :value.',
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'Este :attribute já está em uso.',

    'attributes' => [
        'categoria_id' => 'categoria',
        'codigo' => 'código',
        'descricao' => 'descrição',
        'estoque_minimo' => 'estoque mínimo',
        'password' => 'senha',
        'preco' => 'preço',
        'produto_id' => 'produto',
        'username' => 'usuário',
    ],
];