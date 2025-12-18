# NAT VPS Manager

A Laravel-based web application for managing Virtualizor NAT VPS instances across multiple servers. Built with Laravel 12, Tailwind CSS, and Alpine.js.

## Features

### Admin Features
- **Server Management** - Add, edit, delete Virtualizor servers with API credentials
- **NAT VPS Management** - Create, update, delete NAT VPS records
- **User Management** - Create users, assign VPS, reset passwords
- **Dashboard** - Overview of servers, VPS instances, and system health

### User Features
- **VPS Overview** - View assigned NAT VPS with specs (CPU, RAM, disk, bandwidth)
- **Power Actions** - Start, stop, restart, poweroff VPS instances
- **Domain Forwarding** - Manage HTTP/HTTPS VDF rules
- **SSH Credentials** - View stored SSH access information

### Security
- Role-based access control (Admin/User)
- Encrypted storage for API keys and SSH credentials
- Session-based authentication

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ & NPM
- MySQL 8.0+

## Installation

```bash
# Clone repository
git clone https://github.com/iam-rizz/Laravel-NATVPS-Manager.git
cd Laravel-NATVPS-Manager

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_DATABASE=nat_vps_manager
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start server
php artisan serve
```

## Project Structure

```
app/
├── Enums/
│   ├── UserRole.php          # Admin/User roles
│   └── DomainProtocol.php    # HTTP/HTTPS protocols
├── Models/
│   ├── User.php              # User with role
│   ├── Server.php            # Virtualizor server
│   ├── NatVps.php            # NAT VPS instance
│   └── DomainForwarding.php  # VDF rules
├── Libraries/
│   └── Virtualizor/
│       └── enduser.php       # Virtualizor API wrapper
```

## Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Tailwind CSS, Alpine.js
- **Database**: MySQL
- **API**: Virtualizor Enduser API

## TODO

- [ ] Authentication system (login/logout)
- [ ] Admin middleware
- [ ] Virtualizor API service wrapper
- [ ] Server CRUD (Admin)
- [ ] NAT VPS CRUD (Admin)
- [ ] User management (Admin)
- [ ] User VPS view & power actions
- [ ] Domain forwarding management
- [ ] Admin & User dashboards
- [ ] Mobile responsive layout
- [ ] Database seeder with default admin

## Contributing

Pull requests are welcome. For major changes, please open an issue first.

## License

[GNU General Public License v3.0](LICENSE)
