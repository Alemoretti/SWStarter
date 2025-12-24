# SWStarter

App to return data from the Star Wars universe.

## Prerequisites

- Docker
- Docker Compose
- Git

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd SWStarter
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Start Docker containers:
```bash
docker compose up -d --build
```

4. Generate application key:
```bash
docker compose exec app php artisan key:generate
```

5. Run migrations:
```bash
docker compose exec app php artisan migrate
```

6. Install frontend dependencies:
```bash
docker compose exec app npm install
```

7. Build frontend assets:
```bash
docker compose exec app npm run build
```

## Accessing the Application

- Web: http://localhost:8000
- MySQL: localhost:3306
  - Database: swstarter
  - Username: swstarter
  - Password: password

## Development

### Running Tests
```bash
docker compose exec app php artisan test
```

### Running Frontend Dev Server
```bash
docker compose exec app npm run dev
```
