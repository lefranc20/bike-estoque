# 🚲 Bike Estoque

Sistema de controle de estoque para uma revendedora de peças de bicicleta: cadastro de produtos e categorias, controle de quantidade e registro de entradas, saídas e ajustes de estoque, com dashboard de indicadores, gráfico de movimentações por período, exportação de relatórios em CSV/PDF e controle de acesso por papéis (admin/padrão).

**🔗 Demo online:** [bike-estoque.vercel.app](https://bike-estoque.vercel.app/)
![GIF de demonstração](https://github.com/user-attachments/assets/1476a541-ea5e-476a-b230-cc60840fe683)
>Demo


---

## Tecnologias Utilizadas

[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/) [![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/) [![Laravel Sanctum](https://img.shields.io/badge/Sanctum-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum) [![Vue 3](https://img.shields.io/badge/Vue-3-4FC08D?logo=vuedotjs&logoColor=white)](https://vuejs.org/) [![Vite](https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=white)](https://vite.dev/) [![SQLite (local)](https://img.shields.io/badge/SQLite-local-07405E?logo=sqlite&logoColor=white)](https://www.sqlite.org/) [![PostgreSQL (produção)](https://img.shields.io/badge/PostgreSQL-produção-4169E1?logo=postgresql&logoColor=white)](https://www.postgresql.org/) [![CI](https://github.com/lefranc20/bike-estoque/actions/workflows/ci.yml/badge.svg)](https://github.com/lefranc20/bike-estoque/actions/workflows/ci.yml)


---

## Como rodar localmente

### Backend

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate

type nul > database\database.sqlite
php artisan migrate
php artisan db:seed
php artisan serve
```

Antes do `db:seed`, defina `ADMIN_USERNAME`/`ADMIN_PASSWORD` (e, opcionalmente, `PADRAO_USERNAME`/`PADRAO_PASSWORD`) no `.env` — são as credenciais criadas para login local.

API disponível em `http://127.0.0.1:8000`.

### Frontend

```bash
cd frontend
npm install
```

Crie um `.env` na pasta `frontend` seguindo o exemplo do arquivo `.env.example`.

```bash
npm run dev
```

Aplicação disponível em `http://localhost:5173`.
