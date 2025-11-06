# RESM - SKSU Project

## About RESM Project

RESM (Room/Event Scheduling Management) is a Laravel-based application for managing room bookings and event scheduling.

## Setup Options

Choose one of the following setup methods:

### Option 1: Docker Setup (Recommended for Quick Start)

This method includes PostgreSQL and pgAdmin pre-configured.

#### Prerequisites
- Docker Desktop or Docker Engine
- Docker Compose

#### Quick Start

```bash
# 1. Copy the Docker environment file
cp .env.docker .env

# 2. Build and start all services
docker-compose up -d --build

# 3. Wait for dependencies to install (check logs)
docker-compose logs -f app

# 4. Once the container is ready, generate application key
docker-compose exec app php artisan key:generate

# 5. Run migrations
docker-compose exec app php artisan migrate

# 6. (Optional) Seed the database
docker-compose exec app php artisan db:seed

# 7. Build frontend assets
docker-compose exec app npm run build
```

**Note**:
- The first startup will take longer as it automatically installs Composer and npm dependencies
- When using Docker, you don't need PHP/Composer/Node.js installed locally
- All `php artisan` commands should be prefixed with `docker-compose exec app`

**Examples**:
```bash
# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Run composer commands
docker-compose exec app composer require package/name

# Run npm commands
docker-compose exec app npm run build
```

**Access the container shell**:
```bash
docker-compose exec app bash
```
Then run commands normally inside the container:
```bash
php artisan migrate
composer install
npm run dev
```

#### Access the Application

- **Application**: http://localhost:8000
- **pgAdmin**: http://localhost:5050 (Login: admin@admin.com / admin, Database auto-configured)
- **Vite Dev Server**: http://localhost:5173 (only when running `npm run dev`)

**Note**: The PostgreSQL database connection is automatically configured in pgAdmin. Just log in and the "RESM Database" server will be ready to use (password: `resm_password`).

For detailed Docker documentation, see [DOCKER.md](DOCKER.md)

---

### Option 2: Traditional Setup

#### System Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- PostgreSQL or SQLite (default)

#### Initial Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install npm packages
npm install

# 3. Setup environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Setup database and run migrations
php artisan migrate:fresh --seed

# 6. Build frontend assets
npm run dev

# 7. Start development server
php artisan serve
```

The application will be available at http://localhost:8000

## Development

### Frontend Development

**Production build** (one-time compilation):
```bash
# Docker
docker-compose exec app npm run build

# Traditional
npm run build
```

**Development mode** (with hot-reload):
```bash
# Docker
docker-compose exec app npm run dev

# Traditional
npm run dev
```

**Note**: Use `npm run dev` during active frontend development for automatic reloading. Use `npm run build` for production.

### Running Tests

```bash
# Docker
docker-compose exec app php artisan test

# Traditional
php artisan test
```

### Common Commands

```bash
# Clear cache
php artisan cache:clear

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Build frontend assets
npm run build

# Watch frontend assets (development)
npm run dev
```

## Troubleshooting

### "vendor/autoload.php not found" error

If you see this error, the dependencies haven't been installed yet. This usually means:

1. **Stop and rebuild the containers**:
   ```bash
   docker-compose down
   docker-compose up -d --build
   ```

2. **Check the logs** to see if dependencies are being installed:
   ```bash
   docker-compose logs -f app
   ```

3. **Manually install dependencies** if needed:
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   ```

### "Vite manifest not found" error

This error occurs when the frontend assets haven't been built yet. To fix:

```bash
# Build the frontend assets for production
docker-compose exec app npm run build
```

**For development** with hot-reload, use:
```bash
docker-compose exec app npm run dev
```

**Note**: You need to build assets after:
- First setup
- Pulling new code with frontend changes
- Modifying frontend files (unless using `npm run dev`)

### pgAdmin server list is empty

If pgAdmin doesn't show the "RESM Database" server automatically:

1. **Clear the pgAdmin volume and restart**:
   ```bash
   docker-compose down
   docker volume rm resm-project_pgadmin_data
   docker-compose up -d
   ```

2. **Or manually add the server** in pgAdmin:
   - Right-click "Servers" → "Create" → "Server"
   - General tab: Name = "RESM Database"
   - Connection tab: Host = db, Port = 5432, Database = resm_db, Username = resm_user, Password = resm_password

### Port already in use

If ports 8000, 5050, 5173, or 5432 are already in use, modify the port mappings in `docker-compose.yml`:
```yaml
ports:
  - "8001:80"  # Change 8000 to 8001
```

## Testing the Backend

### Using Laravel's Built-in Testing

```bash
# Docker
docker-compose exec app php artisan test

# Traditional
php artisan test
```
## Documentation

- [Docker Setup Guide](DOCKER.md) - Detailed Docker configuration and troubleshooting
