# Task Flow API Management

## Dependencies :package:

- Docker :whale:

## Run :rocket:

1. Clone:

```bash
$ git clone https://github.com/thiiagoms/task-flow
$ cd task-flow
task-flow $
```

2. Container setup:

```bash
task-flow $ cp .env.example .env
task-flow $ docker-compose up -d
task-flow $ docker-compose exec app bash
```

3. Install app dependencies:

```bash
thiiagoms@ca644be5c8b5:/var/www$ composer install -vvv
thiiagoms@ca644be5c8b5:/var/www$ php artisan key:generate
thiiagoms@ca644be5c8b5:/var/www$ php artisan jwt:secret
thiiagoms@ca644be5c8b5:/var/www$ php artisan migrate
```

4. Run unit and integration tests:

```bash
thiiagoms@ca644be5c8b5:/var/www$ php artisan test
```

5. Run lint:

```bash
thiiagoms@ca644be5c8b5:/var/www$ composer pint app database tests
```

6. Generate swagger:

```bash
thiiagoms@ca644be5c8b5:/var/www$ php artisan l5-swagger:generate
```

API with Swagger documentation at `http://localhost:8000/api/documentation`
