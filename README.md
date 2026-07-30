# Bike Estoque

Sistema de controle de estoque para revendedora de peças de bicicleta.

Desenvolvido com **Laravel** (API) + **Vue.js** (frontend).

---

## Tecnologias utilizadas

- **Backend:** Laravel 11 + Sanctum
- **Frontend:** Vue 3 + Vite + Vue Router + Axios
- **Banco de dados:** SQLite (fácil de usar em desenvolvimento)
- **Estilo:** CSS puro (simples e direto)

---

## Funcionalidades

- Cadastro de categorias de peças
- Cadastro de produtos (peças de bicicleta)
- Controle de quantidade em estoque
- Estoque mínimo com alerta
- Dashboard com resumo do estoque
- Movimentações de estoque (entrada, saída e ajuste)

---

## Como rodar o projeto

### 1. Pré-requisitos

Você precisa ter instalado:

- PHP 8.2 ou superior
- Composer
- Node.js 18 ou superior
- Extensões do PHP: `fileinfo`, `pdo_sqlite` e `sqlite3`

### 2. Clonar o repositório

```bash
git clone https://github.com/SEU-USUARIO/bike-estoque.git
cd bike-estoque
```

### 3. Configurar o Backend
``` bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
```

Crie o banco de dados SQLite:
``` bash
Bashtype nul > database\database.sqlite
```

Rode as migrations:
``` sh
php artisan migrate
```

Inicie o servidor:
``` bash 
php artisan serve
```

O backend vai rodar em: http://127.0.0.1:8000

### 4. Configurar o Frontend
Abra outro terminal:
``` bash 
cd frontend
npm install
```

Crie o arquivo .env na pasta frontend com o conteúdo:
```
VITE_API_URL=http://127.0.0.1:8000/api
```

Inicie o frontend:
``` bash
npm run dev
```

O frontend vai rodar em: http://localhost:5173