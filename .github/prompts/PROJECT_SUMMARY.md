# Barangay Certificate Request System - Project Overview

Your project is a **digital certificate request and appointment management system** for a barangay (Filipino neighborhood administrative division). It streamlines the process of requesting official documents and scheduling appointments.

---

## Core Features Implemented

### 1. Authentication & User Management
- **User Registration**: New residents can sign up with credentials
- **Login System**: Secure session-based authentication
- **Role-Based Access Control**: Two roles implemented:
  - **Resident**: Regular users who request certificates
  - **Admin**: Administrative staff managing requests and certificates
- **Session Management**: Automatic redirection based on login status and role

### 2. Resident Dashboard & Features
The resident dashboard displays:
- **Request Statistics**: Real-time counts showing:
  - Total requests submitted
  - Pending approvals (⏳)
  - Approved requests (✅)
  - Completed requests (🎉)
  - Rejected requests (❌)

**Resident Actions**:
- **Create Certificate Requests**: Residents fill out a form with:
  - Certificate type (selected from available certificates)
  - Desired appointment date
  - Personal information (full name, civil status, birthday, address, contact number)
  - Address and contact details for verification

- **View Personal Requests**: Track all submitted requests in one place with status and certificate name displayed
- **Request Tracking**: Monitor the progress of each request from submission to completion

### 3. Admin Dashboard & Management
The admin dashboard provides a system overview with:
- **System Statistics**:
  - Total registered residents count
  - Total requests across the system
  - Breakdown of requests by status (pending, approved, completed, rejected)
  - Recent request activity (last 8 requests)

**Admin Actions**:
- **Manage Certificate Requests**: View all resident requests with details, update request status, add remarks/feedback
- **Manage Certificates**: Create new certificate types, delete certificates as needed
- **Request Approval Workflow**: Change request status (Pending → Approved → Completed or Rejected)

### 4. Landing Page
- Professional home page for unauthenticated users
- Feature highlights (Fast Processing, Easy Scheduling, Real-time Status Tracking)
- Call-to-action buttons for registration and login
- Responsive design showcasing benefits

---

## Technical Architecture & Flow

### Database Models

1. **User Model**: Stores user credentials, roles, and information
2. **Request Model**: Manages certificate requests with methods:
   - `getAll()`: Retrieves all requests (for admin)
   - `getByUser()`: Gets requests for specific resident
   - `create()`: Inserts new request
   - `updateStatus()`: Updates request status and remarks

3. **Certificate Model**: Manages available certificate types
   - `getAll()`: Lists all available certificates

### MVC Controllers

1. **RequestController** (Resident):
   - `createForm()`: Displays form with available certificates and current user info
   - `store()`: Validates and saves new request to database
   - `myRequests()`: Displays resident's request history

2. **AdminController**:
   - `manageRequests()`: Lists all requests for admin review
   - `updateRequest()`: Updates request status and remarks
   - `manageCertificates()`: Lists certificate types
   - `createCertificate()`: Adds new certificate type
   - `deleteCertificate()`: Removes certificate type

### API Endpoints (RESTful JSON API)

- `GET /public/api.php?action=resident-stats`: Gets request statistics for logged-in resident
- `GET /public/api.php?action=my-requests`: Fetches resident's requests as JSON
- `GET /public/api.php?action=admin-stats`: Gets system-wide statistics for admin
- `GET /public/api.php?action=manage-requests`: Gets all requests for admin management
- `GET /public/api.php?action=admin-dashboard-sync`: Real-time dashboard data
- `GET /public/api.php?action=admin-recent-requests`: Latest 8 requests with formatting

**Security**: All API endpoints include **role-based access control** - residents can only access resident endpoints, admins only access admin endpoints.

### Routing System (index.php)

The main entry point implements a simple routing system using URL parameters (`?page=`):
- Routes requests to appropriate controllers or views
- Checks authentication and authorization before loading pages
- Redirects unauthenticated users to login
- Redirects logged-in users away from auth pages

### Request Processing Flow

```
Resident Request Submission:
1. Resident fills request form 
   ↓
2. Submit to RequestController::store()
   ↓
3. Validation check (all fields required)
   ↓
4. Database insert
   ↓
5. Success message displayed
   ↓
6. Redirect to my-requests page

Admin Review Process:
7. Admin views manage-requests
   ↓
8. Sees all resident requests
   ↓
9. Updates status via form
   ↓
10. Remarks saved
    ↓
11. Redirect back
```

### Data Security

- PDO prepared statements to prevent SQL injection
- Session-based authentication
- Role verification before access
- Input validation before database operations
- Error handling with HTTP status codes (401 Unauthorized, 403 Forbidden)

---

## Key Design Patterns Used

- **MVC Architecture**: Separation of Models, Views, Controllers
- **OOP Design**: Classes for Certificate, Request, and Controllers
- **Prepared Statements**: Secure database queries
- **Dependency Injection**: Controllers receive `$pdo` instance
- **RESTful API**: JSON endpoints for data operations
- **Session-Based Auth**: PHP sessions for user state

---

## User Experience Highlights

- **Responsive Design**: Uses Tailwind CSS for mobile-friendly interface
- **Real-time Statistics**: Dashboard cards update with request data
- **Clear Status Indicators**: Color-coded status badges and emoji icons
- **Intuitive Navigation**: Clear menu options for residents and admins
- **Form Validation**: Required fields check before submission
- **Success Messages**: User feedback via session messages

---

## Project Structure

```
config/
├── database.php          # Database connection configuration
└── paths.php            # Path configurations

controllers/
├── AdminController.php   # Admin management logic
├── AuthController.php    # Authentication logic
├── RegisterController.php # User registration
└── RequestController.php  # Request handling

models/
├── Certificate.php      # Certificate data model
├── Request.php          # Request data model
└── User.php             # User data model

public/
├── api.php              # RESTful API endpoints
├── index.php            # Main routing entry point
├── login.php            # Login page
├── logout.php           # Logout handler
├── register.php         # Registration page
└── assets/              # CSS, JS, images

views/
├── landing-page.php     # Home page
├── admin/
│   ├── dashboard.php    # Admin dashboard
│   ├── manage-certificate.php
│   └── manage-request.php
├── auth/
│   ├── login.php        # Login form
│   └── register.php     # Registration form
├── layout/
│   ├── footer.php       # Common footer
│   └── header.php       # Common header
└── resident/
    ├── create-request.php  # Request form
    ├── dashboard.php       # Resident dashboard
    └── my-request.php      # View requests
```

---

## Summary

This is a well-structured PHP project that demonstrates solid fundamentals including:
- Database operations with PDO
- Session management and authentication
- Form handling and validation
- Role-based access control
- RESTful API design
- MVC architectural pattern
- Responsive UI with Tailwind CSS

The system provides a complete workflow for residents to request barangay certificates and for administrators to manage those requests efficiently, reducing paperwork and improving accessibility to government services.
