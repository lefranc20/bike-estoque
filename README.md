# 🚲 Bike Estoque

Sistema de controle de estoque para uma revendedora de peças de bicicleta: cadastro de produtos e categorias, controle de quantidade e registro de entradas, saídas e ajustes de estoque.

**🔗 Demo online:** [bike-estoque.vercel.app](https://bike-estoque.vercel.app/)

---

## Tecnologias

<p align="left">
  <img src="https://skillicons.dev/icons?i=php,laravel,vue,vite,sqlite,postgres" alt="PHP, Laravel, Vue, Vite, SQLite, PostgreSQL" />
</p>

PHP 8.3+ · Laravel 11 + Sanctum · Vue 3 · Vite · SQLite (local) · PostgreSQL (produção)

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
php artisan serve
```

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
