# NAT VPS Manager

A Laravel-based web application for managing Virtualizor NAT VPS instances across multiple servers.

## Features

- Multi-server Virtualizor API integration
- Role-based access control (Admin/User)
- NAT VPS lifecycle management with power actions (start, stop, restart, poweroff)
- Domain forwarding (VDF) configuration
- Secure credential storage with encryption
- Mobile-responsive UI using Tailwind CSS + Alpine.js

## Requirements

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 8.0+

## Installation

1. Clone the repository
```bash
git clone https://github.com/iam-rizz/Laravel-NATVPS-Manager.git
cd Laravel-NATVPS-Manager
```

2. Install PHP dependencies
```bash
composer install
```

3. Install Node dependencies
```bash
npm install
```

4. Copy environment file and configure
```bash
cp .env.example .env
```

5. Generate application key
```bash
php artisan key:generate
```

6. Configure database in `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nat_vps_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations
```bash
php artisan migrate
```

8. Build assets
```bash
npm run build
```

9. Start the development server
```bash
php artisan serve
```

## Tech Stack

- Laravel 12
- Tailwind CSS
- Alpine.js
- MySQL
- Virtualizor Enduser API

## License

MIT License
