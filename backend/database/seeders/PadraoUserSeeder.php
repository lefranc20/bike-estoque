<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PadraoUserSeeder extends Seeder
{
    /**
     * Cria um usuário padrão de teste a partir de PADRAO_USERNAME/PADRAO_PASSWORD, se definidos.
     * Idempotente: seguro rodar em todo deploy, não duplica o usuário.
     */
    public function run(): void
    {
        $username = env('PADRAO_USERNAME');
        $password = env('PADRAO_PASSWORD');

        if (! $username || ! $password) {
            return;
        }

        User::firstOrCreate(
            ['username' => $username],
            [
                'name' => env('PADRAO_NAME', 'Funcionário'),
                'email' => env('PADRAO_EMAIL', "{$username}@bike-estoque.local"),
                'password' => $password,
                'role' => 'padrao',
            ]
        );
    }
}
