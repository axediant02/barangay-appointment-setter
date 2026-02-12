# Deploying BrgyPortal to Render

This guide explains how to deploy the BrgyPortal PHP application (the repository in this workspace) to Render.com as a Web Service, and how to provision a managed database for it. It also includes sample environment variable names, a minimal database schema, local test commands, and common troubleshooting tips.

---

## Prerequisites

- A Render account (https://render.com).
- GitHub (or Git provider) repo containing this project and a branch to deploy from.
- Composer installed locally to prepare the repository (recommended).
- A managed database (MySQL or PostgreSQL) created in Render or another provider.

Notes about this repository:
- The document root is the `public/` folder.
- PHP code uses PDO (project already creates `$pdo` from `config/database.php`). Ensure `config/database.php` reads database credentials from environment variables in production.

---

## 1) Prepare the repo (recommended changes)

The project will run fine on Render as a simple PHP web service, but make these small improvements before deploying:

- Add `composer.json` if you plan to use Composer libraries or autoloading.
- Update `config/database.php` to use environment variables (example below).
- Add a `.env.example` to show required env vars (do NOT commit real secrets).
- Consider moving uploaded files to S3 or similar (Render service filesystem is ephemeral unless you add persistent disk).

Example `config/database.php` snippet (use this pattern so Render env vars are used):

```php
<?php
$driver = getenv('DB_DRIVER') ?: 'mysql';
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'brgyportal';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

if ($driver === 'pgsql') {
    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
} else {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    die('Database connection error');
}
```

Add a `.env.example` listing the variables you will set on Render:

```
# .env.example
DB_DRIVER=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=brgyportal
DB_USERNAME=brgyportal_user
DB_PASSWORD=secret
APP_ENV=production
APP_DEBUG=false
SECRET_KEY=some_random_secret
```

---

## 2) Database schema

Below is a minimal SQL schema for the application tables. Adjust datatypes for your chosen DB (MySQL example):

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('resident','admin') NOT NULL DEFAULT 'resident',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE certificates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  certificate_id INT NOT NULL,
  appointment_date DATE NULL,
  full_name VARCHAR(255) NOT NULL,
  civil_status VARCHAR(50) NULL,
  birthday DATE NULL,
  address TEXT NULL,
  contact_number VARCHAR(50) NULL,
  status VARCHAR(32) NOT NULL DEFAULT 'Pending',
  remarks TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE CASCADE
);

CREATE INDEX idx_requests_user ON requests(user_id);
CREATE INDEX idx_requests_status ON requests(status);
```

How to run migrations:
- If using Render managed DB, open the Database dashboard and use the console connection tools, or run the SQL locally using CLI tools (mysql/psql) connecting to the Render DB.

---

## 3) Create services on Render

1. Log into Render and click `New` → `Web Service`.
2. Connect your Git provider and select the repository and the branch to deploy.
3. For the Environment choose `PHP` (if available) or choose `Docker` if you want a custom runtime.

Render configuration suggestions:
- **Build Command**: `composer install --no-dev --optimize-autoloader` (omit if you don't have composer.json)
- **Start Command**: `php -S 0.0.0.0:$PORT -t public`
  - Render provides the `$PORT` env var; ensure your start command uses it.
- **Instance Type**: choose the plan you need.

4. Click `Create Web Service` and wait for the first deploy to finish.

---

## 4) Environment variables on Render

In your Web Service settings on Render, set the environment variables defined in `.env.example` (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD, etc.).

If you created a managed database in Render, copy the connection details from the Database service page into your Web Service env vars.

Also set `APP_ENV=production` and `APP_DEBUG=false`.

---

## 5) File uploads & sessions

- File uploads: Render service filesystem is ephemeral (it will be wiped on deploy). Use S3-compatible storage (AWS S3, DigitalOcean Spaces) for uploaded files. Update upload paths and include credentials via env vars.
- Sessions: PHP sessions (`session_start()`) default to filesystem storage. This works on Render but session files may be lost between instances. For horizontal scaling you should store sessions in a shared store (Redis) or the database.

---

## 6) Local testing (before pushing to Render)

To test locally with the project public folder as web root:

```bash
# from project root
composer install
php -S localhost:8000 -t public
# open http://localhost:8000
```

Make sure `config/database.php` can connect to your local DB and that tables exist.

---

## 7) Troubleshooting & tips

- 502 / app crash on Render: check service logs (Render Dashboard → Service → Logs) for PHP errors or missing dependencies.
- Port errors: ensure start command uses `$PORT` as shown above.
- Database connection issues: double-check `DB_HOST`, `DB_PORT`, and security/whitelist settings.
- Permissions: uploaded file directories must be writable; prefer external object storage.
- Sessions/Scaling: for multiple instances, use Redis or DB-backed sessions.

---

## 8) Optional improvements for production

- Add `composer.json` and PSR-4 autoloading for cleaner code organization.
- Add `.env` handling with `vlucas/phpdotenv` for local development only (still set env vars on Render manually).
- Add HTTPS redirect and proper cookie flags (`Secure`, `HttpOnly`, `SameSite`) in session configuration.
- Add health check endpoint (simple `GET /health`) so Render can monitor service health.
- Move secrets to Render's Environment variables and never commit secrets to the repo.
- Use CI to run tests and lint before deploying.

---

## 9) Render-specific checklist

- [ ] Repository connected to Render
- [ ] `Start Command` uses `php -S 0.0.0.0:$PORT -t public`
- [ ] Build Command set (if using Composer)
- [ ] Database provisioned and env vars set
- [ ] `.env.example` added to repository
- [ ] Uploads configured to S3 or another persistent store
- [ ] Session store planned for scaling (optional)

---

If you want, I can:
- Create a `composer.json` and add `vlucas/phpdotenv` scaffolding.
- Add a `health.php` endpoint and a simple `render.yaml` (if using Infrastructure as Code).
- Create SQL migration files or a simple migration script you can run against the Render DB.

Tell me which of the optional improvements you'd like me to implement next.
