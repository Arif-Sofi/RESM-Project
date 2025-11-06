# Docker Development Environment

This project includes a complete Docker development environment with PostgreSQL and pgAdmin.

## Services

The Docker setup includes the following services:

1. **Laravel Application** - Main application running on Apache with PHP 8.2
2. **PostgreSQL** - Database server (PostgreSQL 16)
3. **pgAdmin** - Web-based PostgreSQL administration tool

## Prerequisites

- Docker Desktop or Docker Engine installed
- Docker Compose installed

## Quick Start

### 1. Initial Setup

Copy the Docker environment file:

```bash
cp .env.docker .env
```

### 2. Generate Application Key

Before starting the containers, you need to generate an application key. You can do this by temporarily running the Laravel container:

```bash
docker-compose run --rm app php artisan key:generate
```

Or manually add a key to your `.env` file.

### 3. Build and Start Containers

```bash
docker-compose up -d --build
```

This will:
- Build the Laravel application image
- Start all services in detached mode
- Create necessary volumes for data persistence

### 4. Run Database Migrations

```bash
docker-compose exec app php artisan migrate
```

### 5. (Optional) Seed the Database

```bash
docker-compose exec app php artisan db:seed
```

## Accessing Services

Once the containers are running, you can access:

| Service | URL | Credentials |
|---------|-----|-------------|
| Laravel App | http://localhost:8000 | - |
| pgAdmin | http://localhost:5050 | Email: admin@admin.com, Password: admin |
| Vite Dev Server | http://localhost:5173 | Only when running `npm run dev` |
| PostgreSQL | localhost:5432 | Database: resm_db, User: resm_user, Password: resm_password |

## Common Commands

### Start Containers

```bash
docker-compose up -d
```

### Stop Containers

```bash
docker-compose down
```

### Stop Containers and Remove Volumes

```bash
docker-compose down -v
```

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
```

### Execute Artisan Commands

```bash
docker-compose exec app php artisan [command]
```

Examples:
```bash
# Clear cache
docker-compose exec app php artisan cache:clear

# Run migrations
docker-compose exec app php artisan migrate

# Create a new controller
docker-compose exec app php artisan make:controller UserController
```

### Access Laravel Container Shell

```bash
docker-compose exec app bash
```

### Install Composer Dependencies

```bash
docker-compose exec app composer install
```

### Install NPM Dependencies

```bash
docker-compose exec app npm install
```

### Build Frontend Assets

**Production build**:
```bash
docker-compose exec app npm run build
```

**Development mode with hot-reload**:
```bash
docker-compose exec app npm run dev
```
This starts the Vite dev server on http://localhost:5173 with automatic hot-reloading when you modify frontend files.

**Note**: When using `npm run dev`, you may see a message about the dev server running on a different host. This is normal in Docker - the app will still work correctly at http://localhost:8000

## pgAdmin Configuration

The PostgreSQL database server is **automatically configured** in pgAdmin:

1. Open http://localhost:5050
2. Login with the credentials (admin@admin.com / admin)
3. The "RESM Database" server will appear in the left sidebar under "Servers"
4. Click on "RESM Database" to connect
5. Enter the database password when prompted: `resm_password`
6. Check "Save Password" to avoid entering it again

The database connection is pre-configured with:
- Host: db
- Port: 5432
- Database: resm_db
- Username: resm_user

**Note**: The configuration is stored in `docker/pgadmin/servers.json`

## Environment Variables

Key environment variables in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=resm_db
DB_USERNAME=resm_user
DB_PASSWORD=resm_password

PGADMIN_EMAIL=admin@admin.com
PGADMIN_PASSWORD=admin
```

You can customize these values before starting the containers.

## Troubleshooting

### Port Already in Use

If you get a port conflict error, you can change the port mapping in `docker-compose.yml`:

```yaml
ports:
  - "8001:80"  # Change 8000 to 8001 or any available port
```

### Permission Issues

If you encounter permission issues with storage or cache:

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Container Won't Start

Check the logs:
```bash
docker-compose logs app
```

### Database Connection Issues

Make sure the database service is running:
```bash
docker-compose ps
```

If the database isn't ready yet, wait a few seconds and try again.

## Production Considerations

This Docker setup is designed for **development only**. For production:

1. Remove development tools and dependencies
2. Set `APP_DEBUG=false` and `APP_ENV=production`
3. Use proper secrets management (don't commit `.env`)
4. Configure proper SSL/TLS certificates
5. Set up proper logging and monitoring
6. Use production-grade PostgreSQL configuration
7. Implement proper backup strategies

## Clean Up

To completely remove all containers, volumes, and images:

```bash
docker-compose down -v --rmi all
```

Note: This will delete all data in the PostgreSQL database!
