# Guide Wizard Backend - Docker Setup

This directory contains the Docker-based setup for the Guide Wizard Backend application, built on UserFrosting 4.6.

## Quick Start

### 1. Start the Containers

```bash
docker-compose up -d --build
```

### 2. Run the Installation Script

```bash
docker exec guidewizard-php install-guidewizard.sh
```

This script will:
- Clone UserFrosting 4.6
- Clone the Guide Wizard sprinkle from this repository
- Install Composer dependencies (including FormGenerator 4.0)
- Configure the database connection
- Run migrations and create database tables
- Seed the database with initial data (languages, permissions, sample step)
- Create an admin user (you'll be prompted for credentials)
- Set up upload directories with correct permissions

### 3. Access Your Application

- **Frontend**: http://localhost:8080
- **Admin Panel**: http://localhost:8080/admin
- **API Base**: http://localhost:8080/api/

## Configuration

### Environment Variables

Edit `.env` to configure:
- Database credentials
- Domain names for SSL
- Email for Let's Encrypt

### Database Access

MySQL is available on port 3320:
```bash
mysql -h 127.0.0.1 -P 3320 -u guidewizard_user -p
```

## Useful Commands

### View Logs
```bash
docker-compose logs -f
docker-compose logs -f php-guidewizard
docker-compose logs -f nginx-guidewizard
```

### Access PHP Container
```bash
docker-compose exec guidewizard-php bash
```

### Restart Containers
```bash
docker-compose restart
```

### Stop Containers
```bash
docker-compose down
```

### Rebuild After Changes
```bash
docker-compose up -d --build
```

### Run UserFrosting Commands
```bash
docker-compose exec guidewizard-php php bakery migrate
docker-compose exec guidewizard-php php bakery clear-cache
docker-compose exec guidewizard-php composer update
docker-compose exec guidewizard-php npm run dev
```

## SSL Setup

After configuring DNS to point to your server, run:
```bash
cd ../proxy
./setup-ssl.sh your-email@domain.com
```

## Troubleshooting

### Database Connection Issues
Ensure MySQL container is running:
```bash
docker-compose ps
docker-compose logs mysql-guidewizard
```

### Permission Issues
If you encounter permission errors:
```bash
docker-compose exec guidewizard-php chmod -R 775 app/cache app/logs app/sessions app/storage
```

### Reinstall UserFrosting
```bash
docker-compose exec guidewizard-php bash
rm -rf /var/www/html/*
install-guidewizard.sh
```

### View UserFrosting Debug Info
```bash
docker-compose exec guidewizard-php php bakery debug
```

## Architecture

- **Nginx**: Web server (Alpine, 128MB limit)
- **PHP-FPM 8.3**: Application server with UserFrosting requirements (512MB limit)
- **MySQL 8.0**: Database server (1GB limit, port 3320)
- **Networks**: Connected to both internal network and shared `websites-network`

## What's Included

This Docker setup includes:

- **Automated Installation Script**: Handles UserFrosting 4.6 installation, Guide Wizard sprinkle setup, and database configuration
- **PHP 8.3-FPM**: With all required extensions (GD, PDO, MySQL, intl, zip)
- **MySQL 8.0**: Pre-configured database server with optimized settings
- **Nginx**: Alpine-based web server configured for UserFrosting
- **Node.js 18**: For asset compilation (if needed in future)
- **Composer**: Latest version for PHP dependency management
- **Deprecation Warnings Suppressed**: PHP configured to hide PHP 8.3 deprecation notices for better UX

## File Structure

```
docker-setup/
├── docker-compose.yml          # Container orchestration
├── .env                        # Environment configuration
├── nginx/
│   └── default.conf           # Nginx web server config
├── php/
│   ├── Dockerfile             # PHP container with extensions
│   └── install-guidewizard.sh # Automated installation script
├── mysql/
│   ├── data/                  # Database files (created on first run)
│   └── init/                  # SQL initialization scripts
└── www/                       # UserFrosting installation (created by script)
```

## Notes

- UserFrosting files will be in `./www/` directory after installation
- The installation script is embedded in the Docker image at `/usr/local/bin/install-guidewizard.sh`
- Guide Wizard sprinkle is installed at `./www/app/sprinkles/guide_wizard/`
- Upload directory is automatically created at `./www/app/sprinkles/guide_wizard/uploads/images/`
- Database data persists in `./mysql/data/` even when containers are stopped
- The setup uses the `master` branch of the Guide Wizard repository by default
