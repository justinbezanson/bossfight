# Bossfight

## Development

```bash
cp .env.example .env
composer run dev
```

The app runs at `http://localhost:8000` with Vite hot-reload.

## Production

### Prerequisites

- Docker and Docker Compose
- Host nginx proxying `bossfight.local` to `127.0.0.1:8001`

### Setup

```bash
# Copy and configure production environment
cp .env.example .env.production
# Edit .env.production — set APP_KEY, APP_URL, APP_DEBUG=false, etc.

# Build and start
docker compose -f docker-compose.prod.yml up -d --build
```

### Host Nginx Config

```nginx
server {
    listen 80;
    server_name bossfight.local;

    location / {
        proxy_pass http://127.0.0.1:8001;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Rebuilding After Code Changes

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

### Viewing Logs

```bash
docker compose -f docker-compose.prod.yml logs -f app
# or
docker compose -f docker-compose.prod.yml exec app tail -n 150 storage/logs/laravel.log
```

### Common Commands

```bash
# Enter the container shell
docker compose -f docker-compose.prod.yml exec app sh

# Run artisan commands
docker compose -f docker-compose.prod.yml exec app php artisan <command>

# Run database migrations
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```
