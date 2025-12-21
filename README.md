<div align="center">

# NAT VPS Manager v2.0

**Web-based control panel for managing Virtualizor NAT VPS instances**

Built with Laravel 12 · Tailwind CSS · Alpine.js

[![Stars](https://img.shields.io/github/stars/iam-rizz/Laravel-NATVPS-Manager?color=C9CBFF&labelColor=1A1B26&style=for-the-badge)](https://github.com/iam-rizz/Laravel-NATVPS-Manager/stargazers)
[![License](https://img.shields.io/github/license/iam-rizz/Laravel-NATVPS-Manager?color=FCA2AA&labelColor=1A1B26&style=for-the-badge)](https://github.com/iam-rizz/Laravel-NATVPS-Manager/blob/main/LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)

[Features](#features) · [Installation](#installation) · [Console Access](#console-access) · [Configuration](#configuration)

</div>

---

<div align="center">

### Recommended Hosting

**[HostData.id](https://hostdata.id)** - Affordable hosting solutions

[![NAT VPS](https://img.shields.io/badge/NAT%20VPS-IDR%2015K/mo-00C851?style=flat-square)](https://hostdata.id/nat-vps)
[![VPS Indonesia](https://img.shields.io/badge/VPS%20Indonesia-IDR%20200K/mo-007ACC?style=flat-square)](https://hostdata.id/vps-indonesia)
[![Shared Hosting ID](https://img.shields.io/badge/Shared%20Hosting%20ID-IDR%2010K/mo-FF6B6B?style=flat-square)](https://hostdata.id/web-hosting-indonesia/)
[![Shared Hosting SG](https://img.shields.io/badge/Shared%20Hosting%20SG-IDR%2015K/mo-9B59B6?style=flat-square)](https://hostdata.id/web-hosting-singapura/)

</div>

---

## Overview

NAT VPS Manager is a control panel for VPS providers using Virtualizor with NAT networking. It provides a user-friendly interface for end-users to manage their VPS without direct access to the Virtualizor panel.

| For Providers | For End-Users |
|---------------|---------------|
| Multi-server management | Self-service VPS control |
| User access control | VNC & SSH Web Console |
| API credential security | Domain forwarding setup |
| Audit logging | Real-time monitoring |

---

## Features

### v2.0 New Features
- 🖥️ **VNC Console** - Browser-based VNC access via noVNC
- 💻 **SSH Web Terminal** - Browser-based SSH via xterm.js
- 🔗 **Unified Routes** - Simplified URL structure (`/dashboard`, `/vps`, `/console`)
- 🎨 **Improved UI** - Consistent theme across all pages

### Core Features
- **Multi-Server Support** - Connect unlimited Virtualizor servers
- **VPS Management** - Full CRUD, bulk import, user assignment
- **Power Controls** - Start, stop, restart, poweroff
- **Domain Forwarding** - HTTP/HTTPS/TCP port forwarding via VDF
- **Two-Factor Auth** - TOTP with recovery codes
- **Audit Logging** - Track all user activities
- **Multi-Language** - English & Indonesian
- **Dark/Light Mode** - Theme toggle with persistence
- **Email Notifications** - VPS events, resource warnings
- **Profile Management** - Edit profile, change password, 2FA setup

---

## Tech Stack

| Category | Technology |
|----------|------------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Tailwind CSS 3, Alpine.js 3 |
| Database | MySQL 8.0+ / SQLite |
| VNC Client | noVNC 1.6 |
| SSH Client | xterm.js 5 |
| Build | Vite 6 |

---

## Routes

### Unified Routes (All Users)
| Route | Description |
|-------|-------------|
| `/dashboard` | Dashboard (role-based view) |
| `/vps` | VPS list |
| `/vps/{id}` | VPS detail & power controls |
| `/vps/{id}/domain-forwarding` | Domain forwarding |
| `/console` | Console selection |
| `/console/{id}` | VNC/SSH terminal |

### Admin Only Routes
| Route | Description |
|-------|-------------|
| `/servers` | Server management |
| `/users` | User management |
| `/settings` | App settings |
| `/audit-logs` | Audit logs |

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php      # Unified dashboard (admin/user)
│   ├── VpsController.php            # Unified VPS management
│   ├── ConsoleController.php        # VNC & SSH console
│   ├── DomainForwardingController.php
│   ├── Admin/                       # Admin-only controllers
│   │   ├── ServerController.php
│   │   ├── UserController.php
│   │   ├── SettingController.php
│   │   ├── AuditLogController.php
│   │   └── EmailTemplateController.php
│   └── Auth/                        # Authentication
│       ├── AuthController.php
│       ├── ProfileController.php
│       ├── TwoFactorController.php
│       └── ForgotPasswordController.php
├── Services/
│   ├── Virtualizor/                 # Virtualizor API service
│   ├── AuditLogService.php
│   ├── MailService.php
│   └── TwoFactorAuthService.php
└── Models/

resources/views/
├── dashboard/                       # Dashboard views
│   ├── admin.blade.php
│   └── user.blade.php
├── vps/                             # VPS management views
│   ├── index.blade.php              # Admin VPS list
│   ├── show.blade.php               # Admin VPS detail
│   ├── user-index.blade.php         # User VPS list
│   ├── user-show.blade.php          # User VPS detail
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── domain-forwarding.blade.php
├── console/                         # Console views
│   ├── index.blade.php              # Console selection
│   └── show.blade.php               # VNC/SSH terminal
├── admin/                           # Admin-only views
│   ├── servers/
│   ├── users/
│   ├── settings/
│   └── audit-logs/
├── auth/                            # Authentication views
└── components/                      # Blade components

scripts/
├── console-proxy/                   # VNC & SSH WebSocket proxy (Node.js)
└── setup-novnc.sh                   # noVNC setup script
```

---

## Installation

### Requirements
- PHP 8.2+ with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, XML
- Composer 2.0+
- Node.js 18+
- MySQL 8.0+ / SQLite

### Quick Start

```bash
# Clone & install
git clone https://github.com/iam-rizz/Laravel-NATVPS-Manager.git
cd Laravel-NATVPS-Manager
composer install --no-dev
npm install

# Configure
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed
php artisan storage:link  

# Build assets
npm run build

# Setup noVNC (for VNC Console)
chmod +x scripts/setup-novnc.sh
./scripts/setup-novnc.sh

# Set permissions
chmod -R 775 storage bootstrap/cache
```

### Default Login
| Field | Value |
|-------|-------|
| Email | `admin@example.com` |
| Password | `password` |

> ⚠️ Change the default password immediately after first login.

---

## Console Access

### Console Proxy

A unified WebSocket proxy that handles both VNC and SSH connections in a single server.

**Architecture:**
```
Browser (noVNC)    → WSS /websockify → Console Proxy → TCP → VNC Server (5900+)
Browser (xterm.js) → WSS /ssh        → Console Proxy → SSH → NAT VPS
```

### Deployment Options

#### Option 1: VPS/Dedicated Server (Recommended)

Run console-proxy on same server with Nginx/Apache reverse proxy.

```bash
cd scripts/console-proxy
npm install
pm2 start server.js --name console-proxy
```

`.env`:
```env
CONSOLE_PROXY_ENABLED=true
CONSOLE_PROXY_PUBLIC_HOST=yourdomain.com
```

#### Option 2: Shared Hosting with Node.js

If your shared hosting supports Node.js (via Phusion Passenger, cPanel Node.js, etc.):

1. Deploy Laravel app on main domain: `yourdomain.com`
2. Create subdomain for console proxy: `console.yourdomain.com`
3. Deploy `scripts/console-proxy/` as Node.js app on the subdomain
4. Configure Laravel `.env`:

```env
CONSOLE_PROXY_ENABLED=true
CONSOLE_PROXY_PUBLIC_HOST=console.yourdomain.com
CONSOLE_PROXY_SSL=true
```

**cPanel Node.js Setup:**
- Application root: `scripts/console-proxy`
- Application URL: `console.yourdomain.com`
- Application startup file: `server.js`
- Run NPM Install from cPanel

#### Option 3: External Server

Deploy console-proxy on a separate cheap VPS:

```bash
git clone https://github.com/iam-rizz/Laravel-NATVPS-Manager.git
cd Laravel-NATVPS-Manager/scripts/console-proxy
npm install
cp .env.example .env
# Edit .env: CONSOLE_PROXY_PORT=6080
pm2 start server.js --name console-proxy
```

Laravel `.env`:
```env
CONSOLE_PROXY_PUBLIC_HOST=your-proxy-server.com:6080
CONSOLE_PROXY_SSL=false  # or true if using SSL
```

#### Option 4: Disable Console

If you cannot run the console proxy:

```env
CONSOLE_PROXY_ENABLED=false
```

The Console menu will be hidden from the sidebar.

**Production with PM2:**
```bash
cd scripts/console-proxy
pm2 start server.js --name console-proxy
pm2 save
pm2 startup
```

**Endpoints:**
| Endpoint | Description |
|----------|-------------|
| `/websockify/HOST/PORT` | VNC WebSocket proxy |
| `/ssh` | SSH WebSocket proxy |
| `/health` | Health check |

### Laravel Environment

Add to `.env`:

```env
# Console Proxy
CONSOLE_PROXY_ENABLED=true
CONSOLE_PROXY_HOST=127.0.0.1
CONSOLE_PROXY_PORT=6080
CONSOLE_PROXY_PUBLIC_HOST=your-domain.com
CONSOLE_PROXY_SSL=true
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/nat-vps-manager/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Console Proxy (VNC & SSH)
    location ~ ^/(websockify|ssh) {
        proxy_pass http://127.0.0.1:6080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 86400;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration

Enable required modules first:
```bash
sudo a2enmod rewrite proxy proxy_http proxy_wstunnel
sudo systemctl restart apache2
```

Virtual host configuration:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/nat-vps-manager/public

    <Directory /var/www/nat-vps-manager/public>
        AllowOverride All
        Require all granted
    </Directory>

    # Console Proxy (VNC & SSH)
    ProxyPass /websockify ws://127.0.0.1:6080/websockify
    ProxyPassReverse /websockify ws://127.0.0.1:6080/websockify
    ProxyPass /ssh ws://127.0.0.1:6080/ssh
    ProxyPassReverse /ssh ws://127.0.0.1:6080/ssh

    ErrorLog ${APACHE_LOG_DIR}/natvps-error.log
    CustomLog ${APACHE_LOG_DIR}/natvps-access.log combined
</VirtualHost>
```

---

## Configuration

### Adding Virtualizor Server

1. Login as admin
2. Go to **Servers** → **Add Server**
3. Fill: Name, IP, API Key, API Password, Port (4083)
4. Click **Test Connection**
5. Save

### Getting Virtualizor API Credentials

1. Login to Virtualizor Admin Panel
2. Go to **Configuration** → **API Credentials**
3. Create or copy existing API Key & Password

### Domain Forwarding Requirements

Ensure your Virtualizor server is configured with:
- HAProxy enabled
- VDF (Virtual Domain Forwarding) enabled
- Source IPs configured
- Port ranges defined

### Email Configuration

Configure via Admin Settings (`/settings/mail`) or `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### Task Scheduler

For automatic resource monitoring:

```bash
# Add to crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Troubleshooting

### API Connection Failed
- Verify API credentials di Virtualizor panel
- Check port 4083 accessible
- Check firewall rules

### VPS Specs Not Loading
```bash
php artisan cache:clear
```

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Console Not Connecting
- Ensure console proxy is running: `pm2 status`
- Check Nginx/Apache WebSocket config
- Verify `.env` proxy settings

---

## Changelog

### v2.0.0
- VNC Console (noVNC)
- SSH Web Terminal (xterm.js)
- Unified routes (`/dashboard`, `/vps`, `/console`)
- Removed duplicate admin/user routes
- Improved console UI
- Code cleanup & optimization

### v1.3.0
- Two-Factor Authentication (TOTP)
- Audit Logging with export
- Multi-Language (EN/ID)
- Profile Management
- Dark/Light Mode
- Forgot Password

### v1.2.0
- Email Notifications
- Email Templates
- Resource Monitoring
- Admin Settings Panel

### v1.1.0
- Domain Forwarding (VDF)
- Settings Management

### v1.0.0
- Initial release

---

## Roadmap

- [ ] Automated Backup Management
- [ ] REST API for External Integrations
- [ ] Billing System Integration
- [ ] Reseller Panel
- [ ] Advanced Monitoring (graphs, alerts)
- [ ] Bulk Operations

---

## Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

---

## License

GNU General Public License v3.0 - see [LICENSE](LICENSE)

---
