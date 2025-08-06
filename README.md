# 📝 Task Flow API Management

A clean, containerized Laravel REST API for managing tasks — with JWT authentication, Swagger documentation, and full test coverage.

## ⚙️ Tech Stack

* 💎 Laravel 11 (Clean Architecture)
* 🐳 Docker + Docker Compose
* 🔐 JWT Auth
* 🧪 PHPUnit for Testing (Integration & Unit Tests)
* 📘 Swagger (via L5-Swagger)

## 🚀 Quickstart

### 1️⃣ Clone the repo

```bash
git clone https://github.com/thiiagoms/laravel-clean-architecture.git task-flow
cd task-flow
```

### 2️⃣ Build and start the containers

```bash
make build
```

☝️ This runs everything you need:

> * Spins up Docker containers
> * Installs PHP dependencies
> * Generates app key & JWT secret
> * Runs migrations

### 3️⃣ Run tests 🧪

```bash
make test
```

Or if you want to be fancy:

```bash
make test-unit       # Unit tests only
make test-feature    # Feature tests only
```

### 4️⃣ Run linter ✨

```bash
make lint
```

Formats and checks your code using [Laravel Pint](https://laravel.com/docs/10.x/pint).

### 5️⃣ Generate Swagger Docs 📚

```bash
make docs
```

Docs will be available at: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

## 🧰 Available Makefile Commands

| Command             | Description                           |
|---------------------|---------------------------------------|
| `make build`        | Build and start the Docker containers |
| `make test`         | Run all tests (unit + feature)        |
| `make test-unit`    | Run only unit tests                   |
| `make test-feature` | Run only feature tests                |
| `make lint`         | Run Laravel Pint linter               |
| `make docs`         | Generate Swagger documentation        |
| `make down`         | Stop and remove containers            |
| `make tinker`       | Open Laravel Tinker REPL              |
| `make logs`         | View container logs                   |

