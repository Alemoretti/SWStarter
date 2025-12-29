# SWStarter

App to return data from the Star Wars universe.

## Prerequisites

- Docker 20.10+
- Docker Compose 2.0+
- Git
- Node.js 20.x LTS
- PHP 8.2+

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

4. Install PHP dependencies:
```bash
docker compose exec app composer install
```

5. Generate application key:
```bash
docker compose exec app php artisan key:generate
```

6. Run migrations:
```bash
docker compose exec app php artisan migrate
```

7. Install frontend dependencies:
```bash
docker compose exec app npm install
```

8. Build frontend assets:
```bash
docker compose exec app npm run build
```

## Environment Configuration

After copying `.env.example` to `.env`, configure the following:

### Database
- `DB_DATABASE=swstarter`
- `DB_USERNAME=swstarter`
- `DB_PASSWORD=password`

### Redis
- `REDIS_CLIENT=phpredis`
- `REDIS_HOST=redis`
- `REDIS_PORT=6379`
- `REDIS_PASSWORD=` (leave empty for no password)
- `REDIS_DB=0` (default database)
- `REDIS_CACHE_DB=1` (cache database)
- `QUEUE_CONNECTION=redis`
- `CACHE_STORE=redis`

### SWAPI (IMPORTANT)
- `SWAPI_BASE_URL=https://swapi.dev/api` (or `https://swapi.info/api` if swapi.dev is unavailable).
- `SWAPI_CACHE_TTL=3600` (Cache TTL in seconds, default: 3600)
- `SWAPI_TIMEOUT=10` (Request timeout in seconds, default: 10)
- `SWAPI_RETRY_TIMES=3` (Number of retry attempts, default: 3)
- `SWAPI_RETRY_DELAY=100` (Retry delay in milliseconds, default: 100)

I left swapi.info uncommented in the env.example because swapi.dev was having certificate issues until the day I finished this assignment and asked via email. Since I didn't get a response yet, I recommend using `swapi.info`. The project deals with both responses from them. The only difference is that swapi.dev returns a results key and the swapi.info returns the direct array.

### Search Configuration
- `SEARCH_PER_PAGE=10` (Number of results per page, default: 10)

### Statistics Configuration
- `STATISTICS_CACHE_TTL=5` (Statistics cache TTL in minutes, default: 5)
- `STATISTICS_JOB_DEBOUNCE_SECONDS=60` (Debounce period for statistics recomputation job, default: 60)

## Accessing the Application

- Web: http://localhost:8000
- MySQL: localhost:3306
  - Database: swstarter
  - Username: swstarter
  - Password: password
- Redis: localhost:6379

## Running Queue Workers

The application uses Redis for background job processing. Start the queue worker:

```bash
docker compose exec app php artisan queue:work
```

For development with auto-restart:
```bash
docker compose exec app php artisan queue:listen
```

To process failed jobs:
```bash
docker compose exec app php artisan queue:retry all
```

## Laravel Scheduler

The application runs a scheduled task every 5 minutes to recompute search statistics.

In production, add this to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

For local development, you can test the scheduler:
```bash
docker compose exec app php artisan schedule:work
```

## API Endpoints

### Search
- **Endpoint**: `GET/POST /api/v1/search`
- **Parameters**: 
  - `query` (string, required): Search term
  - `type` (string, required): `people` or `movies`
  - `page` (integer, optional): Page number (default: 1)
- **Response**: JSON with search results and pagination metadata
- **Example**: `/api/v1/search?query=luke&type=people&page=1`

### Character Details
- **Endpoint**: `GET /api/v1/characters/{id}`
- **Response**: JSON with character details and associated movies
- **Example**: `/api/v1/characters/1`

### Movie Details
- **Endpoint**: `GET /api/v1/movies/{id}`
- **Response**: JSON with movie details and associated characters
- **Example**: `/api/v1/movies/1`

### Statistics
- **Endpoint**: `GET /api/v1/statistics`
- **Response**: JSON with search statistics
  - `top_queries`: Top 5 search queries with percentages and type (people/movies)
  - `avg_response_time_ms`: Average API response time in milliseconds
  - `popular_hour`: Most popular hour of day for searches

Statistics response example:

<img width="373" height="760" alt="image" src="https://github.com/user-attachments/assets/51086dfa-c932-4781-84ac-763612e04c3e" />


## Development

### Running Tests

#### Backend Tests (PHPUnit/Pest)
```bash
docker compose exec app php artisan test
```

#### Frontend Tests (Vitest)
```bash
docker compose exec app npm test
```

#### Run All Tests
```bash
docker compose exec app php artisan test && docker compose exec app npm test
```

### Code Quality

#### Backend (Laravel Pint)
```bash
docker compose exec app ./vendor/bin/pint
```

#### Frontend (ESLint)
```bash
docker compose exec app npm run lint
docker compose exec app npm run lint:fix
```

### Running Frontend Dev Server
```bash
docker compose exec app npm run dev
```

## Screenshot

<img width="1154" height="757" alt="image" src="https://github.com/user-attachments/assets/768c6409-7e62-47c8-bfd4-d47d67f55114" />


## Troubleshooting

Some of the issues I had when developing this project, these commands helped me to solve them.

### Redis Connection Issues
If you encounter Redis connection errors:
1. Check if Redis container is running: `docker compose ps redis`
2. Test Redis connection: `docker compose exec redis redis-cli ping`
3. Verify environment variables in `.env`
4. Restart Redis: `docker compose restart redis`

### Queue Jobs Not Processing
- Ensure queue worker is running: `docker compose exec app php artisan queue:work`
- Check for failed jobs: `docker compose exec app php artisan queue:failed`
- Retry failed jobs: `docker compose exec app php artisan queue:retry all`

### Cache Issues
- Clear application cache: `docker compose exec app php artisan cache:clear`
- Clear config cache: `docker compose exec app php artisan config:clear`
- Clear route cache: `docker compose exec app php artisan route:clear`
- Clear view cache: `docker compose exec app php artisan view:clear`

### Database Issues
- Run migrations: `docker compose exec app php artisan migrate`
- Reset database (⚠️ deletes all data): `docker compose exec app php artisan migrate:fresh`

### Frontend Build Issues
- Clear node_modules and reinstall: 
  ```bash
  docker compose exec app rm -rf node_modules package-lock.json
  docker compose exec app npm install
  ```
- Rebuild assets: `docker compose exec app npm run build`

### Permission Issues
- Fix storage permissions: `docker compose exec app chmod -R 775 storage bootstrap/cache`
