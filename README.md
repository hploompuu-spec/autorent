# Car Rental Web Application

A PHP-based car rental information system containerized with Docker.

## Quick Start

### Using Docker Compose (Recommended)

```bash
docker compose up -d
```

Access the application:
- **Web App:** http://localhost/
- **phpMyAdmin:** http://localhost:8080/

### Credentials

**phpMyAdmin:**
- Username: `root`
- Password: `docker123`

**Database User:**
- Username: `hannes`
- Password: `Passw0rd`

## Services

- **PHP/Apache**: Web server (port 80)
- **MariaDB**: Database (port 3306)
- **phpMyAdmin**: Database management (port 8080)

## Features

- Car listing with sorting and pagination
- User registration and authentication
- Car rental booking system
- Admin dashboard
- Responsive Bootstrap UI

## Project Structure

```
car_rent/
├── Dockerfile           # PHP/Apache container definition
├── docker-compose.yml   # Multi-container orchestration
├── .dockerignore        # Docker build exclusions
├── config.php           # Database configuration
├── index.php            # Home page with car listing
├── register.php         # User registration
├── admin/               # Admin panel
├── db/                  # Database dumps
└── header.php           # Shared header template
```

## Development

Edit PHP files in VS Code — changes sync automatically to the container via bind mount.

## Stop Containers

```bash
docker compose down
```

Remove data (reset database):

```bash
docker compose down -v
```

## License

Educational project.
