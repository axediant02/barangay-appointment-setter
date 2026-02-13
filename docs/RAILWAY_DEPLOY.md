# Railway Deployment Plan & Guide — Barangay Certificate System

This guide walks you through deploying the **Barangay Certificate System** (PHP) to [Railway](https://railway.app) with a MySQL database.

---

## Table of Contents

1. [Overview](#1-overview)
2. [Prerequisites](#2-prerequisites)
3. [Pre-Deploy Checklist (Code & Config)](#3-pre-deploy-checklist-code--config)
4. [Railway Project Setup](#4-railway-project-setup)
5. [Add MySQL Database](#5-add-mysql-database)
6. [Create the Web Service](#6-create-the-web-service)
7. [Environment Variables](#7-environment-variables)
8. [Database Schema (First Run)](#8-database-schema-first-run)
9. [Deploy & Verify](#9-deploy--verify)
10. [Custom Domain (Optional)](#10-custom-domain-optional)
11. [Troubleshooting](#11-troubleshooting)
12. [Railway Deploy Checklist](#12-railway-deploy-checklist)

---

## 1. Overview

| Item | Detail |
|------|--------|
| **App** | Barangay Certificate System (PHP, session-based auth, MySQL) |
| **Document root** | `public/` |
| **Entry point** | `public/index.php` (routing via `?page=...`) |
| **Database** | MySQL (PDO) |

Railway will:

- Build and run your PHP app (Nixpacks or Railpack).
- Expose a public URL and optionally a custom domain.
- Provide a MySQL instance and inject connection details via environment variables.

---

## 2. Prerequisites

- [Railway account](https://railway.app) (GitHub login supported).
- This repo on **GitHub** (or another connected Git provider).
- **config/database.php** updated to use environment variables (see [Section 3](#3-pre-deploy-checklist-code--config)).

---

## 3. Pre-Deploy Checklist (Code & Config)

### 3.1 Use environment variables for the database

Your app must read DB credentials from the environment so Railway can inject them.

**Update `config/database.php`** to something like:

```php
<?php
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'barangay-appointment';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
}
```

Railway’s MySQL plugin may expose variables with different names (e.g. `MYSQLHOST`, `MYSQLUSER`). In that case use those names in `getenv()` or add a small mapping (see [Section 7](#7-environment-variables)).

### 3.2 Optional: `.env.example`

Keep a `.env.example` in the repo (no real secrets) so you know which variables to set on Railway:

```env
# Database (set via Railway MySQL reference or manually)
DB_HOST=
DB_PORT=3306
DB_NAME=barangay-appointment
DB_USER=root
DB_PASSWORD=

# App
APP_ENV=production
```

---

## 4. Railway Project Setup

1. Go to [railway.app](https://railway.app) and sign in (e.g. with GitHub).
2. Click **New Project**.
3. Choose **Deploy from GitHub repo** and select this repository and the branch you want to deploy (e.g. `main`).

You will add two services to this project:

- A **MySQL** database.
- A **Web Service** for the PHP app.

---

## 5. Add MySQL Database

1. In the project, click **+ New**.
2. Select **Database** → **MySQL**.
3. Railway creates a MySQL service and exposes connection details as variables.
4. Open the MySQL service → **Variables** (or **Connect**). Note the variable names (e.g. `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`). You will reference these in the web service.

---

## 6. Create the Web Service

1. In the same project, click **+ New** → **GitHub Repo** (or **Empty Service** and connect the repo later).
2. Select this repository and branch.

### 6.1 Root directory

- In the service **Settings**, set **Root Directory** to the repo root (leave blank if the repo root is the project root).
- The document root for the web server must be `public/`. That is done via the **Start Command** (see below).

### 6.2 Build

- **Builder**: Railway will detect PHP (Nixpacks or Railpack). You usually don’t need a custom build command.
- If you use Composer, add a **Build Command** (e.g. `composer install --no-dev --optimize-autoloader`). Otherwise leave it empty.
- If you get “could not find driver” for MySQL, ensure PDO MySQL is enabled. With Nixpacks you can set:
  - **NIXPACKS_PHP_EXTENSIONS** = `pdo_mysql` (or `mysqli,pdo_mysql` if you use both).

### 6.3 Start command (important)

Railway assigns a **PORT**; your app must listen on `0.0.0.0:PORT` and serve from `public/`.

In the web service **Settings** → **Deploy** (or **Start Command**), set:

```bash
php -S 0.0.0.0:$PORT -t public
```

- `-t public` makes `public/` the document root so `index.php` is the entry point.

### 6.4 Watch paths (optional)

If you want deploys only when certain paths change, set **Watch Paths** to e.g. `public/*,config/*,controllers/*,models/*,views/*`.

---

## 7. Environment Variables

The PHP app needs database credentials. Two approaches:

### Option A: Reference MySQL variables (recommended)

1. Open your **Web Service** (PHP app).
2. Go to **Variables**.
3. Click **+ New Variable** → **Add Reference** (or **Reference**).
4. Select the **MySQL** service.
5. Railway adds references like `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`.

Then in `config/database.php` use those names, for example:

```php
$host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'railway';
$user = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
```

(Adjust to the exact variable names shown in your MySQL service.)

### Option B: Manual variables

If you use a different MySQL host (e.g. external), set in the web service:

| Variable      | Example value        | Description   |
|---------------|----------------------|---------------|
| `DB_HOST`     | your-db-host         | MySQL host    |
| `DB_PORT`     | 3306                 | MySQL port    |
| `DB_NAME`     | barangay-appointment | Database name |
| `DB_USER`     | root                 | DB user       |
| `DB_PASSWORD` | (secret)             | DB password   |

### App environment (optional)

- `APP_ENV=production`
- `APP_DEBUG=0` or `false` (do not enable in production)

---

## 8. Database Schema (First Run)

After the first deploy, the app will fail until the database has tables. Run the schema once against the Railway MySQL instance.

### Option A: Railway MySQL shell

1. Open the MySQL service in Railway.
2. Use the **Connect** tab / **MySQL shell** (or the connection string they provide).
3. Create the database if needed (Railway often creates one per MySQL service).
4. Run your schema SQL (see below).

### Option B: Local client

Use the connection details from the MySQL service (host, port, user, password, database) in a local MySQL client (TablePlus, DBeaver, `mysql` CLI) and run the schema.

### Minimal schema (MySQL)

Adjust if your app already has migrations; this matches the structure expected by the app:

```sql
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'resident',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS certificates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  certificate_id INT NOT NULL,
  appointment_date DATE,
  full_name VARCHAR(255) NOT NULL,
  civil_status VARCHAR(50),
  birthday DATE,
  address TEXT,
  contact_number VARCHAR(50),
  status VARCHAR(32) NOT NULL DEFAULT 'Pending',
  remarks TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (certificate_id) REFERENCES certificates(id)
);

CREATE INDEX idx_requests_user_id ON requests(user_id);
CREATE INDEX idx_requests_status ON requests(status);
```

Then insert at least one admin user (hash with `password_hash()` in PHP if needed) and optional certificate types.

---

## 9. Deploy & Verify

1. Push the branch that Railway is watching (e.g. `main`). Railway will build and deploy.
2. Open the web service → **Settings** → **Networking** → **Generate Domain** to get a public URL.
3. Open the URL in a browser:
   - You should see the landing or login page.
   - If you see “Database connection failed”, check [Section 11](#11-troubleshooting).
4. Test: register, log in (resident/admin), create a request, and (if applicable) use admin flows.

---

## 10. Custom Domain (Optional)

1. In the web service, go to **Settings** → **Networking** → **Custom Domain**.
2. Add your domain and follow Railway’s instructions to set the CNAME (or A record).
3. Railway will provide HTTPS for the custom domain.

---

## 11. Troubleshooting

### 502 Bad Gateway (fixed in repo)

The repo now includes:

- **`railway.json`** – Sets the start command to `php -S 0.0.0.0:$PORT -t public` so the app listens on Railway’s `PORT`.
- **`config/database.php`** – Reads credentials from env vars (`MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE` or `DB_*`). **You must** link your MySQL service to the web service: Web Service → **Variables** → **Add Reference** → select your MySQL service so these variables are set.
- **`composer.json`** – Declares `ext-pdo_mysql` so the PHP build includes the MySQL driver.

After pulling these changes, redeploy. If 502 persists, check **Deployments → View Logs** for PHP or DB errors.

| Issue | What to check |
|-------|----------------|
| **502 / App not responding** | Logs (Railway service → **Deployments** → latest → **View Logs**). Ensure start command is `php -S 0.0.0.0:$PORT -t public` and that `$PORT` is used. |
| **Database connection failed** | Env vars: correct host, port, database, user, password. If using reference, ensure the MySQL service is in the same project and variables are referenced. |
| **Could not find driver (PDO MySQL)** | Enable PHP extensions: set `NIXPACKS_PHP_EXTENSIONS=pdo_mysql` (and `mysqli` if needed) and redeploy. |
| **404 on all routes** | Document root must be `public/`. Start command must include `-t public`. |
| **Sessions / login lost** | PHP default sessions are file-based; redeploys or multiple instances can lose them. For production, consider DB-backed or Redis sessions. |
| **Blank page** | Enable logging: in PHP, `error_reporting(E_ALL); ini_set('display_errors', 1);` temporarily and check logs; fix the error then turn display_errors off again. |

---

## 12. Railway Deploy Checklist

Use this before and after going live:

**Before deploy**

- [ ] `config/database.php` uses `getenv()` for DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD (or Railway’s MYSQL* variables).
- [ ] `.env.example` lists required variables (no real secrets committed).
- [ ] Start command is `php -S 0.0.0.0:$PORT -t public`.
- [ ] NIXPACKS_PHP_EXTENSIONS includes `pdo_mysql` if you see driver errors.

**Railway project**

- [ ] New project created; repo connected.
- [ ] MySQL service added; variables noted or referenced.
- [ ] Web service created from same repo; root directory correct.
- [ ] Build command set if using Composer.
- [ ] Start command set as above.
- [ ] Web service variables: DB credentials (or MySQL reference) and optional APP_ENV/APP_DEBUG.

**After first deploy**

- [ ] Schema and seed data applied to Railway MySQL.
- [ ] Public URL opens; landing/login loads.
- [ ] Login, register, and one request flow tested.
- [ ] Custom domain added (optional) and DNS verified.

---

## Quick reference

| What | Value |
|------|--------|
| Doc root | `public/` |
| Start command | `php -S 0.0.0.0:$PORT -t public` |
| DB config | `config/database.php` (use env vars) |
| Routes | `?page=login`, `?page=register`, `?page=resident-dashboard`, etc. |

For more: [Railway Docs](https://docs.railway.app), [Railway Help](https://help.railway.app).
