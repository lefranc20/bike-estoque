<?php

namespace App\Http\Middleware;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful as SanctumMiddleware;

/**
 * O middleware original do Sanctum força session.same_site = 'lax' em toda
 * requisição, ignorando SESSION_SAME_SITE do .env. Isso impede o cookie de
 * sessão de ser aceito quando frontend e backend estão em domínios
 * diferentes (ex.: Vercel + Render). Aqui, só deixamos de forçar o valor.
 */
class EnsureFrontendRequestsAreStateful extends SanctumMiddleware
{
    protected function configureSecureCookieSessions(): void
    {
        config([
            'session.http_only' => true,
            'session.same_site' => config('session.same_site', 'lax'),
        ]);
    }
}
