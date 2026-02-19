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
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'resident') DEFAULT 'resident',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Certificates Table
```sql
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) DEFAULT 0.00,
    requirements TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Requests Table
```sql
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    certificate_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    civil_status VARCHAR(50),
    birthday DATE,
    address TEXT,
    contact_number VARCHAR(20),
    status ENUM('Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled') DEFAULT 'Pending',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE CASCADE
);
```

## 3. Populate Certificates
```sql
INSERT INTO certificates (id, name, price, requirements) VALUES
(1, 'Barangay Clearance', 50.00, 'Valid ID'),
(2, 'Indigency Certificate', 0.00, 'None'),
(3, 'Business Permit', 500.00, 'DTI Registration, Lease Contract'),
(4, 'Residency Certificate', 50.00, 'Valid ID, Proof of Billing'),
(5, 'Good Moral Certificate', 50.00, 'Valid ID'),
(6, 'Community Tax Certificate', 20.00, 'Valid ID'),
(7, 'Barangay ID', 100.00, 'Valid ID, 1x1 Picture');
```

## 4. Create Admin Account
**Username:** `admin`
**Password:** `admin123`

```sql
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@barangay.com', '$2y$10$/L7mJohsvtDLRuo5Qe.6fOLNn4bvhWy9MHu5CE18LPXMXSXpfMFFO', 'admin');
```
