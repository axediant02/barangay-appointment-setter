# Database Setup Commands

Run the following SQL commands in your database manager (e.g., phpMyAdmin) to set up the database, tables, and default data.

## 1. Create Database
```sql
CREATE DATABASE IF NOT EXISTS barangay_db;
USE barangay_db;
```

## 2. Create Tables

### Users Table
```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Certificates Table
```sql
CREATE TABLE certificates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Requests Table
```sql
CREATE TABLE requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    certificate_id INT UNSIGNED NOT NULL,
    appointment_date DATE NOT NULL,
    status ENUM('Pending', 'Approved', 'Completed', 'Rejected', 'Cancelled') 
        NOT NULL DEFAULT 'Pending',

    full_name VARCHAR(100) NOT NULL,
    civil_status ENUM('Single', 'Married', 'Widowed', 'Divorced') DEFAULT 'Single',
    birthday DATE NULL,
    address VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    remarks TEXT NULL,
    id_image_path VARCHAR(255) NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_user (user_id),
    INDEX idx_certificate (certificate_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),

    CONSTRAINT fk_request_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_request_certificate
        FOREIGN KEY (certificate_id) REFERENCES certificates(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Update Existing Tables (If needed)
If you already have the `requests` table, run this command to add the ID content column:
```sql
ALTER TABLE requests
    ADD COLUMN id_image_path VARCHAR(255) NULL AFTER remarks;
```

## 3. Populate Certificates
```sql
INSERT INTO certificates (name, description, created_at, updated_at) VALUES
('Barangay Clearance', 'Official clearance from the barangay', NOW(), NOW()),
('Indigency Certificate', 'Proof of indigency for residents', NOW(), NOW()),
('Business Permit', 'Certificate for business registration', NOW(), NOW()),
('Residency Certificate', 'Proof of residency within the barangay', NOW(), NOW()),
('Good Moral Certificate', 'Issued for school or work purposes', NOW(), NOW()),
('Community Tax Certificate', 'Also known as Cedula, proof of payment of community tax', NOW(), NOW()),
('Barangay ID', 'Official identification card issued by the barangay', NOW(), NOW());
```

## 4. Admin Account Setup

You can set up the initial admin account using one of the two methods below.

### Method A: Quick Setup (Recommended)
Use this SQL command to create an admin account with the default password `Admin@123`.

*   **Username:** `admin`
*   **Password:** `Admin@123`

```sql
INSERT INTO users (username, email, password, role, created_at, updated_at)
VALUES (
    'admin',
    'admin@barangay.com',
    '$2y$10$wH9z3YkXvZ0n8pQvRk6T8O8QfR1Xk1fV9lFz8uE1r0QwBz7mY3JrK',
    'admin',
    NOW(),
    NOW()
);
```

---

### Method B: Custom Admin Credentials
If you prefer a custom password, follow these refined steps:

#### 1. Generate the Password Hash
Run this PHP snippet in your local environment to generate a secure hash:
```php
<?php
echo password_hash('YourSecretPassword', PASSWORD_DEFAULT);
```

#### 2. Execute SQL with Your Hash
Replace the email and password hash below with your desired values:

```sql
INSERT INTO users (username, email, password, role, created_at, updated_at)
VALUES (
    'admin',
    'admin@example.com',
    'PASTE_YOUR_GENERATED_HASH_HERE',
    'admin',
    NOW(),
    NOW()
);
```

> [!IMPORTANT]
> Change the default email `admin@barangay.com` to your actual administrative email for system notifications.
