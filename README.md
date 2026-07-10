# AgriTech Pro — Smart Agriculture Platform

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

**AgriTech Pro** is a comprehensive digital agriculture platform connecting farmers, buyers, instructors, and agri-innovators. Built with Laravel 13, it empowers farmers through education, marketplace trade, real-time delivery tracking, AI-powered disease detection, and community innovation sharing.

---

## Features

- **Learning Center** — 300+ farming courses with video, PDF, and text lessons; progress tracking; certificates
- **Agri Marketplace** — Buy and sell seeds, fertilizers, produce, livestock, tools, and equipment
- **Real-Time Delivery Tracking** — GPS-based tracking with SMS updates and driver mobile app
- **AI Disease Detection** — Upload plant photos for instant disease identification with treatment recommendations
- **Innovation Hub** — Farmers share and vote on agricultural innovations with competition prizes
- **SMS Services** — Outbreak alerts, order updates, and farming tips via SMS
- **Weather Integration** — Hyper-local forecasts and planting condition alerts
- **Smart Notifications** — In-app and SMS notifications for orders, courses, and disease alerts
- **Admin Panel** — Comprehensive dashboard for managing farmers, products, orders, courses, and SMS broadcasts
- **Mobile First** — Fully responsive design optimized for feature phones and smartphones
- **Dark Mode** — System-wide dark/light theme toggle
- **AI Chatbot** — Built-in farming assistant for instant help

## Requirements

- **PHP** 8.3 or higher
- **Composer** 2.x
- **Database** — MySQL 8.0+ / MariaDB 10.6+ / PostgreSQL 15+ / SQLite
- **Node.js** 20+ (for frontend asset building)
- **Extensions** — BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL, GD

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-org/agritech-pro.git
cd agritech-pro
```

### 2. Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your database and other services:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agritech
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### 4. Database migration & seeding

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Install & build frontend assets

```bash
npm install --ignore-scripts
npm run build
```

### 6. Storage link

```bash
php artisan storage:link
```

### 7. Queue worker (required for SMS, notifications, delivery tracking)

```bash
php artisan queue:work --queue=high,default --tries=3 --timeout=60
```

### 8. Start the server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## Quick Start (using the built-in setup script)

```bash
composer setup
```

This runs `composer install`, copies `.env`, generates an app key, runs migrations, and builds frontend assets.

### Development mode

```bash
composer dev
```

Runs the Laravel dev server, queue listener, log watcher, and Vite hot-reload concurrently.

---

## Configuration

### Key Environment Variables

| Variable | Description | Default |
|---|---|---|
| `APP_NAME` | Application name | AgriTech |
| `APP_ENV` | Environment (local/production) | local |
| `APP_DEBUG` | Debug mode | true |
| `APP_URL` | Application URL | http://localhost |
| `DB_CONNECTION` | Database driver | sqlite |
| `SESSION_DRIVER` | Session storage | database |
| `QUEUE_CONNECTION` | Queue driver | database |
| `CACHE_STORE` | Cache driver | database |
| `MAIL_MAILER` | Mail driver | log |

### SMS Service

Configure your SMS provider in `config/services.php`:

```env
SMS_PROVIDER=africastalking
SMS_API_KEY=your_api_key
SMS_SENDER_ID=AgriTech
```

### AI Disease Detection

The disease detection service uses a configurable AI endpoint. Set in `.env`:

```env
DISEASE_API_URL=https://your-ai-api.com/predict
DISEASE_API_KEY=your_api_key
```

### Mobile Money / Payment Gateway

```env
PAYMENT_PROVIDER=airtelmoney
PAYMENT_API_KEY=your_api_key
PAYMENT_WEBHOOK_SECRET=your_secret
```

---

## Database Schema

The system uses 20+ tables. Key models:

- **User** — Farmers, instructors, admins, drivers (role-based access)
- **Farm** — Farm profile linked to each farmer
- **FarmProduction** — Crop yield and production records
- **Course** — Farming courses with categories and access types
- **Lesson** — Course content (video, PDF, text, quiz)
- **Enrollment** — Student course enrollment and progress tracking
- **CourseReview** — Student ratings and reviews
- **CourseGuide** — PDF field guides for offline access
- **Product** — Marketplace listings with pricing and inventory
- **Order** — Buyer orders with payment and fulfillment tracking
- **OrderItem** — Individual items within orders
- **Delivery** — Real-time delivery tracking with GPS
- **DeliveryStatusLog** — Status change history
- **Driver** — Delivery driver profiles
- **Innovation** — Farmer innovation submissions
- **InnovationVote** — Community voting
- **Disease** — Disease library with treatments
- **DiseaseDetection** — AI detection history
- **DiseaseAlert** — SMS outbreak alerts
- **Notification** — In-app notifications
- **District** — Geographic regions with trading centres
- **SmsCampaign** — SMS broadcast campaigns
- **SmsLog** — SMS delivery logs
- **Setting** — System configuration
- **Wishlist** — Marketplace wishlist items

---

## Architecture

```
app/
├── Console/Commands/       # Artisan commands
├── Http/
│   ├── Controllers/        # Request handlers
│   ├── Middleware/          # Role-based access control
│   └── Requests/           # Form validation requests
├── Models/                 # Eloquent models
├── Providers/              # Service providers
└── Services/               # Business logic (SMS, payments, disease detection)

resources/
├── views/
│   ├── layouts/            # Base layout (app.blade.php)
│   ├── pages/              # Page templates
│   └── partials/           # Reusable components (navbar, footer, chatbot, etc.)

routes/
└── web.php                 # All application routes

public/
├── css/                    # Stylesheets
├── js/                     # JavaScript
└── assets/                 # SVG and static assets

config/                     # Application configuration
database/
├── migrations/             # Schema migrations
├── factories/              # Model factories
└── seeders/                # Database seeders
```

## Roles

| Role | Capabilities |
|---|---|
| **Farmer** | Browse & enroll in courses, buy/sell products, submit innovations, detect diseases, track deliveries |
| **Instructor** | Create and manage courses, lessons, and PDF guides |
| **Driver** | Update delivery statuses, share GPS location |
| **Admin** | Full system management — CRUD all resources, SMS broadcasts, settings |

## Testing

```bash
composer test
```

Runs PHPUnit tests with a clean config.

```bash
php artisan test --coverage
```

## Deployment

### Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Generate optimized cache:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. Set up a queue worker as a systemd service
4. Configure your web server (Nginx/Laravel Forge/Envoyer)
5. Set up SSL certificate
6. Configure cron for scheduled tasks:
   ```bash
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit your changes: `git commit -am 'Add new feature'`
4. Push the branch: `git push origin feature/my-feature`
5. Submit a pull request

Please ensure your code follows PSR-12 coding standards and includes appropriate tests.

## Security

If you discover a security vulnerability, please email the project team rather than opening a public issue.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

Built with ❤️ for Malawian farmers.
