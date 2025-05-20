<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Laravel Docker Setup

This project uses Docker and Docker Compose for development. Follow these steps to set up your development environment.

## Prerequisites

### 1. Install Docker
- **macOS**: Download and install [Docker Desktop](https://www.docker.com/products/docker-desktop)
- **Linux**: Follow the [Docker installation guide](https://docs.docker.com/engine/install/)
- **Windows**: Download and install [Docker Desktop](https://www.docker.com/products/docker-desktop)

### 2. Install Docker Compose
- Docker Compose comes pre-installed with Docker Desktop for macOS and Windows
- For Linux, follow the [Docker Compose installation guide](https://docs.docker.com/compose/install/)

### 3. Install Make
- **macOS**: Install via Homebrew
  ```bash
  brew install make
  ```
- **Linux**: Install via package manager
  ```bash
  # Ubuntu/Debian
  sudo apt-get install make
  
  # CentOS/RHEL
  sudo yum install make
  ```
- **Windows**: Install via [Chocolatey](https://chocolatey.org/)
  ```bash
  choco install make
  ```

## Project Setup

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. Start the application:
   ```bash
   make start
   ```
   This will:
   - Create `.env` file from `.env.example` if it doesn't exist
   - Start the Docker containers

3. If you need to rebuild the containers (after Dockerfile changes):
   ```bash
   make rebuild
   ```
   This will:
   - Stop all running containers
   - Rebuild containers without cache
   - Start the containers again

## Accessing the Application

- Web application: http://localhost:8045
- MySQL database:
  - Host: localhost
  - Port: 3302
  - Database: automation
  - Username: automation
  - Password: automation

## Available Make Commands

- `make start`: Start the application
- `make rebuild`: Rebuild and restart the containers

## Troubleshooting

If you encounter any issues:

1. Check if Docker is running:
   ```bash
   docker --version
   docker-compose --version
   ```

2. Ensure all ports are available:
   - 8045 (Web application)
   - 3302 (MySQL)

3. If containers fail to start:
   ```bash
   docker-compose logs
   ```

4. To completely reset the environment:
   ```bash
   docker-compose down -v
   make rebuild
   ```
