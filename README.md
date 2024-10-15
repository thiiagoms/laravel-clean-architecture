# ESocial API Teste

## Dependências

- Docker :whale:

## Instalação

1. Clone o repositório:

```bash
$ git clone https://github.com/thiiagoms/teste-esocial esocial
$ cd esocial
esocial $
```

2. Setup dos containers:

```bash
esocial $ cp .env.example .env
esocial $ docker-compose up -d
esocial $ docker-compose exec app bash
```

Obs: Talvez seja necessário trocar o `user` e `uid` em `.devops/php/Dockerfile`

3. Setup das dependências da aplicação:

```bash
thiiagoms@ca644be5c8b5:/var/www$ composer install -vvv
thiiagoms@ca644be5c8b5:/var/www$ php artisan key:generate
thiiagoms@ca644be5c8b5:/var/www$ php artisan jwt:secret
thiiagoms@ca644be5c8b5:/var/www$ php artisan migrate
```

4. Executar testes unitários e de integração,:

```bash
thiiagoms@ca644be5c8b5:/var/www$ php artisan test
```

5. Gerar documentação do swagger:

```bash
thiiagoms@ca644be5c8b5:/var/www$ php artisan l5-swagger:generate
```

Documentação servida em `http://localhost:8000/api/documentation`
