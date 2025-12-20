<div align="center">

# NAT VPS Manager

**A comprehensive web application for managing Virtualizor NAT VPS instances**

Built with Laravel 12 · Tailwind CSS · Alpine.js

[![Stars](https://img.shields.io/github/stars/iam-rizz/Laravel-NATVPS-Manager?color=C9CBFF&labelColor=1A1B26&style=for-the-badge)](https://github.com/iam-rizz/Laravel-NATVPS-Manager/stargazers)
[![Size](https://img.shields.io/github/repo-size/iam-rizz/Laravel-NATVPS-Manager?color=9ece6a&labelColor=1A1B26&style=for-the-badge)](https://github.com/iam-rizz/Laravel-NATVPS-Manager)
[![License](https://img.shields.io/github/license/iam-rizz/Laravel-NATVPS-Manager?color=FCA2AA&labelColor=1A1B26&style=for-the-badge)](https://github.com/iam-rizz/Laravel-NATVPS-Manager/blob/main/LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)

[Overview](#overview) · [Features](#features) · [Screenshots](#screenshots) · [Installation](#installation) · [Configuration](#configuration) · [API Reference](#api-reference) · [Roadmap](#roadmap)

</div>

---

<div align="center">

### Recommended Hosting

Looking for reliable VPS hosting to run this application?

**[HostData.id](https://hostdata.id)** provides affordable and reliable hosting solutions.

[![NAT VPS](https://img.shields.io/badge/NAT%20VPS-Starting%20IDR%2015K/mo-00C851?style=flat-square)](https://hostdata.id/nat-vps)
[![VPS Indonesia](https://img.shields.io/badge/VPS%20Indonesia-Starting%20IDR%20200K/mo-007ACC?style=flat-square)](https://hostdata.id/vps-indonesia)
[![Dedicated Server](https://img.shields.io/badge/Dedicated%20Server-Enterprise%20Ready-8B5CF6?style=flat-square)](https://hostdata.id/dedicated-server)

---

Made with dedication for the VPS hosting community

</div>

## Overview

NAT VPS Manager is a production-ready web-based control panel designed for VPS providers, resellers, and hosting companies that utilize Virtualizor with NAT (Network Address Translation) networking. It bridges the gap between the Virtualizor admin panel and end-users by providing a streamlined, user-friendly interface for managing virtual private servers.

### The Problem

When running NAT VPS services, providers face several challenges:
- End-users cannot access Virtualizor panel directly for security reasons
- Managing port forwarding rules requires technical knowledge
- No centralized way to manage multiple Virtualizor servers
- Difficulty tracking VPS assignments and user access

### The Solution

NAT VPS Manager addresses these challenges by providing:
- **Isolated User Portal** - Customers manage only their assigned VPS without panel access
- **Simplified Port Forwarding** - Intuitive interface for HTTP/HTTPS/TCP forwarding rules
- **Multi-Server Dashboard** - Single pane of glass for all your Virtualizor servers
- **User Assignment System** - Easy VPS-to-user mapping with access control

### Key Benefits

| For Providers | For End-Users |
|---------------|---------------|
| Centralized multi-server management | Self-service VPS control |
| Reduced support tickets | Easy domain forwarding setup |
| User access control | Real-time resource monitoring |
| API credential security | One-click power actions |
| Scalable architecture | Mobile-friendly interface |

---

## Features

### Administration Panel

#### Server Management
- **Multi-Server Support** - Connect unlimited Virtualizor servers
- **Encrypted Credentials** - API keys stored with AES-256 encryption
- **Connection Testing** - Verify API connectivity before saving
- **Server Health Monitoring** - Track server status and VPS count
- **Automatic Discovery** - List all VPS from connected servers

#### VPS Management
- **Full CRUD Operations** - Create, read, update, delete VPS records
- **API Synchronization** - Real-time data from Virtualizor API
- **Bulk Import** - Import existing VPS from Virtualizor servers
- **User Assignment** - Assign/unassign VPS to user accounts
- **SSH Credential Storage** - Securely store and display SSH access info
- **Cached Fallback** - Display cached data when API is offline

#### User Management
- **Account Creation** - Create user accounts with email verification
- **Role-Based Access** - Admin and User permission levels
- **Password Management** - Secure password reset functionality
- **VPS Assignment** - Assign multiple VPS to single user
- **Activity Tracking** - Monitor user login and actions

#### Dashboard Analytics
- **System Overview** - Total servers, VPS, users at a glance
- **Server Status** - Online/offline status for each server
- **Recent Activity** - Latest VPS and user operations
- **Quick Actions** - Fast access to common admin tasks

#### Settings Management
- **General Settings** - App name, logo, favicon, timezone configuration
- **Mail Settings** - SMTP configuration with encryption support
- **Test Email** - Verify SMTP settings before going live
- **Notification Toggles** - Enable/disable specific email notifications
- **Email Templates** - Customizable HTML email templates with variables
- **Resource Monitoring** - Configure automatic VPS resource checking
- **Audit Settings** - Configure log retention period

#### Email Notifications
- **VPS Assignment** - Notify users when VPS is assigned/unassigned
- **Power Actions** - Email alerts for start/stop/restart operations
- **Resource Warnings** - Automatic alerts when CPU/RAM/Disk exceeds threshold
- **Welcome Email** - Customizable welcome message for new users
- **Template Variables** - Dynamic content with {{variable}} placeholders

#### Audit Logging (v1.3)
- **Comprehensive Tracking** - Log all user activities (login, logout, VPS actions, user management)
- **Filterable Viewer** - Filter by user, action type, date range
- **Detailed Entries** - View old/new values comparison for changes
- **CSV Export** - Export audit logs for compliance and reporting
- **Configurable Retention** - Set automatic cleanup period (30-365 days)
- **IP & User Agent** - Track request origin and browser information

### User Portal

#### VPS Overview
- **Assigned VPS List** - View all VPS assigned to account
- **Real-time Specifications** - CPU, RAM, Disk, Bandwidth from API
- **Status Indicators** - Running/Stopped/Unknown status badges
- **Server Information** - Associated server name and location
- **UUID Display** - Unique VPS identifier for support

#### Power Controls
- **Start VPS** - Boot up stopped VPS instance
- **Stop VPS** - Graceful shutdown of running VPS
- **Restart VPS** - Reboot VPS without data loss
- **Power Off** - Force shutdown for unresponsive VPS
- **Confirmation Dialogs** - Prevent accidental actions
- **Action Feedback** - Success/error messages with details

#### Domain Forwarding (VDF)
- **Protocol Support** - HTTP, HTTPS, and TCP forwarding
- **Port Configuration** - Custom source and destination ports
- **Domain Mapping** - Map domains to internal VPS ports
- **Rule Management** - Create, edit, delete forwarding rules
- **Port Restrictions** - Respect server-defined port limits
- **Real-time Updates** - Changes applied via Virtualizor API

#### SSH Access
- **Credential Display** - Username, password, port information
- **Password Masking** - Show/hide toggle for security
- **SSH Command** - Ready-to-copy SSH connection string
- **Secure Storage** - Encrypted credential storage

#### Bandwidth Monitoring
- **Usage Tracking** - Current month bandwidth consumption
- **Limit Display** - Total allocated bandwidth
- **Visual Indicators** - Progress bars and percentages
- **Historical Data** - Monthly usage from API

#### Profile Management (v1.3)
- **Edit Profile** - Update name and email address
- **Change Password** - Secure password change with current password verification
- **2FA Management** - Enable/disable two-factor authentication from profile

### Authentication & Security

#### Two-Factor Authentication (v1.3)
- **TOTP Support** - Compatible with Google Authenticator, Authy, Microsoft Authenticator
- **QR Code Setup** - Easy scanning for authenticator apps
- **Manual Entry** - Secret key for manual configuration
- **Recovery Codes** - 8 one-time backup codes
- **Secure Viewing** - Password confirmation to view recovery codes
- **Code Regeneration** - Generate new recovery codes anytime
- **Rate Limiting** - 5 attempts per minute to prevent brute force

#### Password Reset (v1.3)
- **Email-Based Reset** - Secure password reset via email link
- **Token Expiration** - Reset links expire after 60 minutes
- **Rate Limiting** - Prevent abuse of reset functionality

#### Multi-Language Support (v1.3)
- **English** - Full English translation
- **Indonesian** - Complete Bahasa Indonesia translation
- **Language Switcher** - Easy switching on login page
- **Session Persistence** - Language preference saved per session

### Technical Features

#### Security
- **Role-Based Access Control** - Granular permission system
- **Two-Factor Authentication** - TOTP-based 2FA with recovery codes
- **Encrypted Storage** - Sensitive data encrypted at rest
- **CSRF Protection** - All forms protected against CSRF
- **SQL Injection Prevention** - Eloquent ORM parameterized queries
- **XSS Protection** - Blade template auto-escaping
- **Session Security** - Secure session handling
- **API Rate Limiting** - Prevent abuse with configurable limits
  - Login: 5 attempts per minute
  - 2FA Verification: 5 attempts per minute
  - VPS Power Actions: 10 per minute per user

#### Performance
- **Lazy Loading** - Efficient database queries with eager loading
- **View Caching** - Compiled Blade templates
- **API Response Caching** - Reduce API calls with smart caching
- **Asset Optimization** - Minified CSS/JS via Vite

#### Reliability
- **Graceful Degradation** - Cached data when API offline
- **Error Handling** - Comprehensive exception handling
- **Logging** - Detailed logs for debugging
- **Database Transactions** - Data integrity protection

#### User Interface (v1.3)
- **Modern Design** - Clean, professional interface with Poppins/Jost typography
- **Dark/Light Mode** - Toggle between themes with localStorage persistence
- **Fixed Sidebar** - Always-visible navigation on desktop
- **Mobile Drawer** - Responsive sidebar for mobile devices
- **Glassmorphism Effects** - Modern visual effects with backdrop blur
- **Toast Notifications** - Beautiful feedback messages with progress bars
- **Consistent Styling** - CSS custom properties for theme consistency

#### Developer Experience
- **Service Layer Architecture** - Clean separation of concerns
- **DTO Pattern** - Type-safe data transfer objects
- **Interface Contracts** - Dependency injection ready
- **PSR-12 Compliant** - Consistent code style
- **Property-Based Testing** - QuickCheck-style tests for critical functions

---

## Screenshots

### Admin Dashboard
```
┌─────────────────────────────────────────────────────────────┐
│  NAT VPS Manager                              Admin ▼       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │ Servers  │  │   VPS    │  │  Users   │  │  Active  │     │
│  │    3     │  │    12    │  │    8     │  │    10    │     │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘     │
│                                                             │
│  Recent Activity                                            │
│  ├─ VPS test.dev assigned to user@example.com               │
│  ├─ Server NAT-SG-01 added                                  │
│  └─ User john@example.com created                           │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### User VPS View
```
┌─────────────────────────────────────────────────────────────┐
│  VPS: myserver.dev                           ● Running      │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  VPS Specifications          │  SSH Credentials             │
│  ─────────────────────────   │  ────────────────────────    │
│  Hostname    myserver.dev    │  Username    root            │
│  VPS ID      103             │  Password    ••••••••        │
│  CPU         2 Core(s)       │  SSH Port    30322           │
│  RAM         6000 MB         │                              │
│  Disk        40 GB           │  SSH Command:                │
│  Bandwidth   4 / 1000 GB     │  ssh root@1.2.3.4 -p 30322   │
│  Server      NAT-SG-01       │                              │
│                                                             │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌───────────┐          │
│  │  Start  │ │  Stop   │ │ Restart │ │ Power Off │          │
│  └─────────┘ └─────────┘ └─────────┘ └───────────┘          │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Tech Stack

| Category | Technology | Version |
|----------|------------|---------|
| **Runtime** | PHP | 8.2+ |
| **Framework** | Laravel | 12.x |
| **Frontend** | Tailwind CSS | 3.x |
| **JavaScript** | Alpine.js | 3.x |
| **Build Tool** | Vite | 6.x |
| **Database** | MySQL / SQLite | 8.0+ / 3.x |
| **API** | Virtualizor Enduser API | - |
| **2FA** | Google2FA | 2.x |

### Dependencies

#### PHP Packages
- `laravel/framework` - Core framework
- `laravel/tinker` - REPL for debugging
- `pragmarx/google2fa-laravel` - Two-factor authentication (TOTP)
- `bacon/bacon-qr-code` - QR code generation for 2FA setup

#### NPM Packages
- `tailwindcss` - Utility-first CSS
- `alpinejs` - Lightweight JS framework
- `autoprefixer` - CSS vendor prefixing
- `vite` - Frontend build tool
- `toastify-js` - Toast notifications

#### Dev Dependencies
- `phpunit/phpunit` - Unit testing
- `steos/quickcheck` - Property-based testing
- `fakerphp/faker` - Test data generation
- `laravel/pint` - Code style fixer

---

## Installation

### System Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP | 8.2 | 8.3 |
| Composer | 2.0 | 2.7+ |
| Node.js | 18.0 | 20.x LTS |
| MySQL | 8.0 | 8.0+ |
| RAM | 512 MB | 1 GB+ |
| Storage | 100 MB | 500 MB+ |

### PHP Extensions Required

```
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_MySQL
- Tokenizer
- XML
```

### Step-by-Step Installation

#### 1. Clone Repository

```bash
git clone https://github.com/iam-rizz/Laravel-NATVPS-Manager.git
cd Laravel-NATVPS-Manager
```

#### 2. Install PHP Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

For development environment:
```bash
composer install
```

#### 3. Install Node Dependencies

```bash
npm install
```

#### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

#### 5. Configure Environment Variables

Edit `.env` file with your settings:

```env
# Application
APP_NAME="NAT VPS Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nat_vps_manager
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file
```

#### 6. Database Setup

```bash
# Create database tables
php artisan migrate

# Seed default admin user (optional)
php artisan db:seed
```

#### 7. Build Frontend Assets

For production:
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

#### 8. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 9. Configure Web Server

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/nat-vps-manager/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Apache .htaccess** (included in `/public`):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### 10. Start Application

Development:
```bash
php artisan serve
```

Production: Configure your web server to point to the `/public` directory.

### Default Credentials

After running `php artisan db:seed`:

| Field | Value |
|-------|-------|
| Email | `admin@example.com` |
| Password | `password` |

**Important:** Change the default password immediately after first login.

---

## Configuration

### Adding Virtualizor Servers

1. Login as admin
2. Navigate to **Servers** > **Add Server**
3. Fill in the required fields:

| Field | Description | Example |
|-------|-------------|---------|
| Name | Display name for the server | `NAT-SG-01` |
| IP Address | Server IP or hostname | `103.xxx.xxx.xxx` |
| API Key | Virtualizor API Key | `xxxxxxxx` |
| API Password | Virtualizor API Password | `xxxxxxxx` |
| Port | API port (default 4083) | `4083` |

4. Click **Test Connection** to verify
5. Save the server configuration

### Getting Virtualizor API Credentials

1. Login to Virtualizor Admin Panel
2. Go to **Configuration** > **API Credentials**
3. Create new API credentials or use existing
4. Copy the API Key and API Password

### Domain Forwarding Setup

For domain forwarding to work, ensure your Virtualizor server has:

1. **HAProxy Enabled** - In Virtualizor settings
2. **VDF Configured** - Virtual Domain Forwarding enabled
3. **Source IPs Set** - Public IPs for HAProxy frontend
4. **Port Ranges Defined** - Allowed ports for forwarding

### Environment Variables Reference

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application display name | `NAT VPS Manager` |
| `APP_ENV` | Environment (local/production) | `production` |
| `APP_DEBUG` | Enable debug mode | `false` |
| `APP_URL` | Application URL | `http://localhost` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `DB_HOST` | Database host | `127.0.0.1` |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name | `nat_vps_manager` |
| `SESSION_DRIVER` | Session storage | `database` |
| `CACHE_DRIVER` | Cache storage | `file` |

### Setting Up Task Scheduler

For automated resource monitoring to work, you need to set up Laravel's task scheduler.

**Add to crontab:**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**For development/testing:**
```bash
php artisan schedule:work
```

**Manual resource check:**
```bash

CPU Usage0.0%
0 / 1,696 MHz

RAM Usage 63.3%
3.71 GB / 5.86 GB

Disk Usage 64.1%
25.66 GB / 40.00 GB

Bandwidth 0.4%
4.15 GB / 1,000 GB

```

### Email Configuration

Email settings can be configured from the Admin Settings panel (`/admin/settings/mail`), but you can also set defaults in `.env`:

```env
# Default SMTP (can be overridden in Settings)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Note:** Settings configured in the Admin panel take precedence over `.env` values.

---

## Project Structure

```
Laravel-NATVPS-Manager/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── MonitorVpsResources.php  # Scheduled resource monitoring
│   │       └── CleanupAuditLogs.php     # Audit log retention cleanup
│   │
│   ├── Enums/
│   │   └── UserRole.php                 # Admin/User role enum
│   │
│   ├── Events/
│   │   ├── TwoFactorSuccess.php         # 2FA success event
│   │   └── TwoFactorFailed.php          # 2FA failure event
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ServerController.php
│   │   │   │   ├── NatVpsController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── AuditLogController.php     # Audit log viewer
│   │   │   │   ├── SettingController.php      # Settings management
│   │   │   │   └── EmailTemplateController.php # Email templates
│   │   │   │
│   │   │   ├── Auth/
│   │   │   │   ├── AuthController.php         # Login/Logout
│   │   │   │   ├── ProfileController.php      # Profile management
│   │   │   │   ├── ForgotPasswordController.php # Password reset
│   │   │   │   ├── TwoFactorController.php    # 2FA setup/management
│   │   │   │   └── TwoFactorChallengeController.php # 2FA verification
│   │   │   │
│   │   │   ├── User/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── VpsController.php
│   │   │   │   └── DomainForwardingController.php
│   │   │   │
│   │   │   └── LanguageController.php   # Language switching
│   │   │
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php      # Admin access control
│   │       ├── SetLocale.php            # Language middleware
│   │       ├── EnsureTwoFactorAuthenticated.php # 2FA middleware
│   │       └── VpsAccessMiddleware.php  # VPS ownership check
│   │
│   ├── Listeners/
│   │   └── AuthEventListener.php        # Auth event logging
│   │
│   ├── Models/
│   │   ├── User.php                     # User with 2FA support
│   │   ├── Server.php                   # Virtualizor server config
│   │   ├── NatVps.php                   # NAT VPS instance
│   │   ├── AuditLog.php                 # Activity audit log
│   │   ├── Setting.php                  # Dynamic settings model
│   │   └── EmailTemplate.php            # Email template model
│   │
│   ├── Services/
│   │   ├── SettingService.php           # Settings helper service
│   │   ├── MailService.php              # Email notification service
│   │   ├── AuditLogService.php          # Audit logging service
│   │   ├── TwoFactorAuthService.php     # 2FA service (TOTP, recovery codes)
│   │   ├── GeoLocation/
│   │   │   └── GeoLocationService.php   # IP geolocation
│   │   └── Virtualizor/
│   │       ├── Contracts/
│   │       │   └── VirtualizorServiceInterface.php
│   │       ├── DTOs/
│   │       │   ├── VpsInfo.php          # VPS data transfer object
│   │       │   ├── ActionResult.php     # API action result
│   │       │   └── ConnectionResult.php # Connection test result
│   │       ├── Exceptions/
│   │       │   └── ConnectionException.php
│   │       └── VirtualizorService.php   # Main API service
│   │
│   ├── Libraries/
│   │   └── Virtualizor/
│   │       └── enduser.php              # Virtualizor API wrapper
│   │
│   └── Providers/
│       └── AppServiceProvider.php       # Service providers & rate limiting
│
├── config/
│   └── app.php                          # Application config
│
├── database/
│   ├── migrations/                      # Database migrations
│   └── seeders/
│       ├── DatabaseSeeder.php           # Main seeder
│       ├── SettingsSeeder.php           # Default settings
│       └── EmailTemplateSeeder.php      # Default email templates
│
├── lang/
│   ├── en/
│   │   └── app.php                      # English translations
│   └── id/
│       └── app.php                      # Indonesian translations
│
├── resources/
│   ├── views/
│   │   ├── admin/                       # Admin panel views
│   │   │   ├── dashboard.blade.php
│   │   │   ├── servers/
│   │   │   ├── nat-vps/
│   │   │   ├── users/
│   │   │   ├── audit-logs/              # Audit log views
│   │   │   │   ├── index.blade.php
│   │   │   │   └── show.blade.php
│   │   │   └── settings/                # Settings views
│   │   │       ├── general.blade.php
│   │   │       ├── mail.blade.php
│   │   │       ├── notifications.blade.php
│   │   │       ├── audit.blade.php
│   │   │       └── email-templates/
│   │   │
│   │   ├── auth/                        # Authentication views
│   │   │   ├── login.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   ├── reset-password.blade.php
│   │   │   ├── profile/
│   │   │   │   └── edit.blade.php       # Profile management
│   │   │   └── two-factor/              # 2FA views
│   │   │       ├── setup.blade.php
│   │   │       ├── challenge.blade.php
│   │   │       └── recovery-codes.blade.php
│   │   │
│   │   ├── user/                        # User portal views
│   │   │   ├── dashboard.blade.php
│   │   │   └── vps/
│   │   │
│   │   ├── components/                  # Blade components
│   │   │   ├── app-layout.blade.php     # Main app layout
│   │   │   ├── guest-layout.blade.php   # Guest layout
│   │   │   ├── sidebar.blade.php        # Sidebar navigation
│   │   │   └── resource-usage.blade.php # VPS resource display
│   │   │
│   │   └── layouts/
│   │       └── app.blade.php            # Legacy layout
│   │
│   ├── css/
│   │   └── app.css                      # Tailwind + custom styles
│   │
│   └── js/
│       ├── app.js                       # Alpine.js + toast setup
│       └── theme.js                     # Dark/light mode toggle
│
├── routes/
│   └── web.php                          # All web routes
│
├── public/
│   └── index.php                        # Application entry
│
├── storage/                             # Logs, cache, sessions
├── tests/                               # PHPUnit + property-based tests
├── .env.example                         # Environment template
├── composer.json                        # PHP dependencies
├── package.json                         # Node dependencies
├── tailwind.config.js                   # Tailwind configuration
├── vite.config.js                       # Vite configuration
└── README.md                            # This file
```

---

## API Reference

### Virtualizor Service Methods

The `VirtualizorService` class provides the following methods:

#### Connection

```php
// Test server connection
$result = $virtualizorService->testConnection(Server $server): ConnectionResult
```

#### VPS Operations

```php
// List all VPS on server
$vpsList = $virtualizorService->listVps(Server $server): array

// Get single VPS info
$vpsInfo = $virtualizorService->getVpsInfo(Server $server, int $vpsId): ?VpsInfo

// Get VPS status (0=stopped, 1=running)
$status = $virtualizorService->getVpsStatus(Server $server, int $vpsId): int
```

#### Power Actions

```php
// Start VPS
$result = $virtualizorService->startVps(Server $server, int $vpsId): ActionResult

// Stop VPS
$result = $virtualizorService->stopVps(Server $server, int $vpsId): ActionResult

// Restart VPS
$result = $virtualizorService->restartVps(Server $server, int $vpsId): ActionResult

// Power off VPS
$result = $virtualizorService->poweroffVps(Server $server, int $vpsId): ActionResult
```

#### Domain Forwarding

```php
// Get forwarding rules
$rules = $virtualizorService->getDomainForwarding(Server $server, int $vpsId): array

// Create forwarding rule
$result = $virtualizorService->createDomainForwarding(
    Server $server, 
    int $vpsId, 
    array $data
): ActionResult

// Update forwarding rule
$result = $virtualizorService->updateDomainForwarding(
    Server $server, 
    int $vpsId, 
    int $recordId, 
    array $data
): ActionResult

// Delete forwarding rule
$result = $virtualizorService->deleteDomainForwarding(
    Server $server, 
    int $vpsId, 
    int $recordId
): ActionResult
```

### Data Transfer Objects

#### VpsInfo

```php
class VpsInfo {
    public readonly int $vpsId;
    public readonly ?string $uuid;
    public readonly ?string $hostname;
    public readonly ?int $cpu;        // Number of cores
    public readonly ?int $ram;        // RAM in MB
    public readonly ?int $disk;       // Disk in GB
    public readonly ?int $bandwidth;  // Bandwidth limit in GB
    public readonly ?int $usedBandwidth; // Used bandwidth in GB
    public readonly ?int $status;     // 0=stopped, 1=running
    public readonly ?array $ips;
    public readonly ?array $rawData;
}
```

#### ActionResult

```php
class ActionResult {
    public readonly bool $success;
    public readonly string $message;
    public readonly ?array $data;
}
```

---

## Roadmap

### Version 1.0 ✅

- [x] User authentication with role-based access
- [x] Admin dashboard with system overview
- [x] Multi-server management with API testing
- [x] NAT VPS CRUD operations
- [x] User management and VPS assignment
- [x] VPS power controls (start/stop/restart/poweroff)
- [x] Domain forwarding management (HTTP/HTTPS/TCP)
- [x] Real-time VPS specs from Virtualizor API
- [x] Cached data fallback for offline scenarios
- [x] Responsive design with dark mode support
- [x] SSH credential display with security masking

### Version 1.1 ✅

- [x] VPS resource usage graphs (CPU, RAM, Network)
- [x] Email notifications for VPS events
- [x] API rate limiting and request throttling
- [x] Improved error messages and user feedback (Toast notifications with progress bar)

### Version 1.2 ✅

- [x] **Admin Settings Panel** - Dynamic configuration from database
  - [x] General Settings (App name, logo, favicon, timezone)
  - [x] Mail Settings (SMTP configuration with test email)
  - [x] Notification Settings (Toggle email notifications)
  - [x] Email Template Editor (Customizable HTML templates)
- [x] **Email Notification System**
  - [x] VPS Assigned notification
  - [x] VPS Unassigned notification
  - [x] VPS Power Action notification (start/stop/restart)
  - [x] Resource Warning notification (CPU/RAM/Disk threshold)
  - [x] Welcome email for new users
- [x] **Automated Resource Monitoring**
  - [x] Scheduled VPS resource checking
  - [x] Configurable check interval (5-60 minutes)
  - [x] Configurable warning thresholds (CPU/RAM/Disk %)
  - [x] Warning cooldown to prevent email spam
- [x] Login/Logout Notifications - Toast messages for authentication events

### Version 1.3 (Current) ✅

- [x] **Multi-language Support**
  - [x] English and Indonesian language packs
  - [x] Language switcher on login page
  - [x] Persistent language preference per session
  - [x] All UI elements translated
- [x] **Two-Factor Authentication (2FA)**
  - [x] TOTP-based authentication (Google Authenticator, Authy compatible)
  - [x] QR code setup with manual entry option
  - [x] 8 recovery codes with secure storage
  - [x] Password-protected recovery code viewing
  - [x] Recovery code regeneration
  - [x] Rate limiting on 2FA verification (5 attempts/minute)
- [x] **User Activity Audit Logging**
  - [x] Comprehensive activity tracking (login, logout, VPS actions, user management)
  - [x] Filterable audit log viewer (by user, action, date range)
  - [x] Detailed log entries with old/new values comparison
  - [x] CSV export functionality
  - [x] Configurable retention period
  - [x] IP address and user agent tracking
- [x] **Modern Theme Update**
  - [x] Redesigned UI with Poppins/Jost typography
  - [x] Dark/Light mode toggle with localStorage persistence
  - [x] Fixed sidebar navigation with mobile drawer
  - [x] Glassmorphism effects and smooth transitions
  - [x] Improved form styling with better contrast
  - [x] Consistent color palette using CSS custom properties
  - [x] Mobile-responsive design improvements
- [x] **Profile Management**
  - [x] Edit profile information (name, email)
  - [x] Change password with current password verification
  - [x] 2FA management from profile page
- [x] **Forgot Password**
  - [x] Email-based password reset
  - [x] Secure token validation with expiration
  - [x] Rate limiting on reset requests

### Version 2.0 (Planned)

- [ ] **VPS Console Access**
  - [ ] noVNC integration for web-based console
  - [ ] Secure WebSocket proxy
  - [ ] Session timeout and security controls
- [ ] **Automated Backup Management**
  - [ ] Scheduled backup configuration
  - [ ] Backup restore functionality
  - [ ] Backup retention policies
- [ ] **Billing System Integration**
  - [ ] Invoice generation
  - [ ] Payment gateway integration (Stripe, PayPal)
  - [ ] Usage-based billing
  - [ ] Credit system
- [ ] **REST API for External Integrations**
  - [ ] API key authentication
  - [ ] Rate limiting per API key
  - [ ] Webhook notifications
  - [ ] OpenAPI/Swagger documentation
- [ ] **Custom Branding & White-label**
  - [ ] Custom logo and favicon upload
  - [ ] Custom color themes
  - [ ] Custom email templates with branding
  - [ ] Remove "Powered by" attribution option
- [ ] **Reseller Panel**
  - [ ] Sub-user management
  - [ ] Resource allocation limits
  - [ ] Reseller-specific pricing
  - [ ] Commission tracking
- [ ] **Automated VPS Provisioning**
  - [ ] Template-based VPS creation
  - [ ] Auto-assignment to users
  - [ ] Post-provisioning scripts
- [ ] **Advanced Monitoring**
  - [ ] Historical resource usage graphs
  - [ ] Custom alert thresholds per VPS
  - [ ] Slack/Discord/Telegram notifications
  - [ ] Uptime monitoring
- [ ] **Bulk Operations**
  - [ ] Bulk VPS power actions
  - [ ] Bulk user assignment
  - [ ] Bulk import/export
- [ ] **Data Export**
  - [ ] Export VPS list to CSV/Excel
  - [ ] Export user data
  - [ ] Export audit logs to PDF

---

## Troubleshooting

### Common Issues

#### API Connection Failed

**Symptom:** "Failed to connect to server" error

**Solutions:**
1. Verify API credentials in Virtualizor panel
2. Check if API port (4083) is accessible
3. Ensure server IP is correct
4. Check firewall rules on Virtualizor server

#### VPS Specs Not Loading

**Symptom:** CPU, RAM, Disk showing as "-"

**Solutions:**
1. Clear application cache: `php artisan cache:clear`
2. Verify VPS ID matches Virtualizor
3. Check API credentials have read permissions

#### Domain Forwarding Not Working

**Symptom:** Rules created but not functioning

**Solutions:**
1. Verify HAProxy is enabled in Virtualizor
2. Check VDF configuration on server
3. Ensure source IPs are correctly set
4. Verify port is not in reserved range

#### Permission Denied Errors

**Symptom:** 500 error or blank page

**Solutions:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Debug Mode

For development, enable debug mode in `.env`:

```env
APP_DEBUG=true
```

**Warning:** Never enable debug mode in production.

### Logs

Application logs are stored in `storage/logs/laravel.log`

```bash
tail -f storage/logs/laravel.log
```

---

## Contributing

We welcome contributions from the community. Here's how you can help:

### Ways to Contribute

- **Bug Reports** - Found a bug? Open an issue with details
- **Feature Requests** - Have an idea? Share it in discussions
- **Code Contributions** - Submit pull requests for improvements
- **Documentation** - Help improve docs and examples
- **Translations** - Help translate to other languages

### Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/Laravel-NATVPS-Manager.git
cd Laravel-NATVPS-Manager

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
npm run dev
php artisan serve
```

### Pull Request Process

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests (`php artisan test`)
5. Commit changes (`git commit -m 'Add amazing feature'`)
6. Push to branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Write tests for new features

---

## Security

### Reporting Vulnerabilities

If you discover a security vulnerability, please send an email to the maintainer instead of opening a public issue. We take security seriously and will respond promptly.

### Security Best Practices

- Always use HTTPS in production
- Keep dependencies updated
- Use strong database passwords
- Regularly rotate API credentials
- Enable firewall on production servers
- Monitor access logs for suspicious activity

---

## License

This project is licensed under the **GNU General Public License v3.0**.

You are free to:
- Use the software for any purpose
- Modify the source code
- Distribute copies
- Distribute modified versions

Under the conditions that:
- Source code must be made available when distributing
- Modifications must be released under GPL-3.0
- Copyright and license notices must be preserved

See the [LICENSE](LICENSE) file for full details.

---

## Acknowledgments

- [Laravel](https://laravel.com) - The PHP framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [Virtualizor](https://virtualizor.com) - VPS control panel
- [Heroicons](https://heroicons.com) - Beautiful hand-crafted SVG icons

---

## Support

- **Documentation:** This README and inline code comments
- **Issues:** [GitHub Issues](https://github.com/iam-rizz/Laravel-NATVPS-Manager/issues)
- **Discussions:** [GitHub Discussions](https://github.com/iam-rizz/Laravel-NATVPS-Manager/discussions)

---

