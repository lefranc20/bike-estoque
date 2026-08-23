<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_com_credenciais_validas_autentica_o_usuario(): void
    {
        $user = User::factory()->create(['password' => 'senha-correta']);

        $response = $this->withHeader('Referer', config('app.url'))->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'senha-correta',
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_com_credenciais_invalidas_falha(): void
    {
        $user = User::factory()->create(['password' => 'senha-correta']);

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'senha-errada',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
    }

    public function test_rota_protegida_exige_autenticacao(): void
    {
        $this->getJson('/api/produtos')->assertUnauthorized();
    }

    public function test_logout_encerra_a_sessao_e_troca_o_cookie(): void
    {
        $user = User::factory()->create(['password' => 'senha-correta']);
        $cliente = $this->withHeader('Referer', config('app.url'));

        $login = $cliente->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'senha-correta',
        ]);
        $login->assertOk();

        $logout = $cliente->postJson('/api/logout');
        $logout->assertNoContent();

        // O cookie de sessão emitido no logout precisa ser diferente do de login
        // (sessão antiga invalidada, id regenerado) — protege contra session fixation.
        $this->assertNotSame(
            $this->sessionCookieValue($login),
            $this->sessionCookieValue($logout),
        );
    }

    private function sessionCookieValue($response): string
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'laravel-session') {
                return $cookie->getValue();
            }
        }

        $this->fail('Cookie laravel-session não encontrado na resposta.');
    }
}
