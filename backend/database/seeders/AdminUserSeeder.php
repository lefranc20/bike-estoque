<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Cria o usuário admin inicial a partir de ADMIN_USERNAME/ADMIN_PASSWORD, se definidos.
     * Idempotente: seguro rodar em todo deploy, não duplica o usuário.
     */
    public function run(): void
    {
        $username = env('ADMIN_USERNAME');
        $password = env('ADMIN_PASSWORD');

        if (! $username || ! $password) {
            return;
        }

        User::firstOrCreate(
            ['username' => $username],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'email' => env('ADMIN_EMAIL', "{$username}@bike-estoque.local"),
                'password' => $password,
                'role' => 'admin',
            ]
        );
    }
}
