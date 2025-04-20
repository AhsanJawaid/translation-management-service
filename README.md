# Installation Guide - Translation Management Service

Welcome to the Translation Management Service! This guide will walk you through the process of installing and running the project locally.

---

## 📦 Requirements

Ensure the following are installed on your system:

- PHP 8.2+
- Composer
- Laravel CLI (optional but helpful)
- MySQL 8.0+
- Git
- Redis (Optional for caching)

---

## 🚀 Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/AhsanJawaid/translation-management-service.git
cd translation-management-service
composer install
```

### 2. Setting up the Application

```bash
cp .env.example .env
php artisan key:generate
Create a database named translation_ms_db in your Database
configure database settings in .env
php artisan migrate --seed
php artisan translations:generate --count=1000 //(100000 for testing load)
php artisan serve
The server will start at http://127.0.0.1:8000
```


### 2. Testing the API

```bash
php artisan tinker
Get an access token:
    curl --request POST \
    --url http://127.0.0.1:8000/api/login \
    --header 'Accept: application/json' \
    --header 'Content-Type: application/json' \
    --data '{
        "email": "test@example.com",
        "password": "password"
    }'

Use the token to access protected endpoints:
    curl --request GET \
    --url http://127.0.0.1:8000/api/translations \
    --header 'Accept: application/json' \
    --header 'Authorization: Bearer YOUR_TOKEN_HERE'

Test the export endpoint
    curl --request GET \
    --url http://localhost:8000/api/translations/export?locale=en \
    --header 'Accept: application/json'
```

## Features

- CRUD operations for translations
- Multi-language support (en, fr, es, etc.)
- Tagging system for contextual organization
- Token-based authentication
- Optimized for performance (<200ms response time)
- JSON export for frontend applications
- Supports 100k+ records
- OpenAPI documentation

## Design Choices

### Architecture
- **SOLID Principles**: Separated concerns with Repository-Service-Controller pattern
- **PSR-12 Compliance**: Consistent coding standards
- **Caching Layer**: Redis-backed for translation exports
- **Optimized Database**: 
  - Composite indexes for frequent queries
  - Properly indexed pivot tables
  - Efficient schema design

### Performance Considerations
- Chunked JSON responses for large datasets
- Database query optimization
- CDN-ready response headers
- Eager loading relationships where needed

### Security
- Sanctum token authentication
- Input validation