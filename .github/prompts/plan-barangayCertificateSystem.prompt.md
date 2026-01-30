# Plan: Barangay Certificate System Implementation

**TL;DR:** Build the system sequentially from foundation (database + config) → models and core logic → controllers and routing → views and forms → admin features and enhancements. Day 1-2 covers database and base setup, Days 3-5 focus on authentication and role-based dashboards, Days 6-8 implement certificate and request CRUD with appointment booking, Days 9-10 polish and testing. Uses PDO prepared statements, Tailwind CSS, URL rewriting, and session-based security throughout.

**Key Decisions Made:**
- CSS Framework: **Tailwind CSS** (utility-first, lightweight)
- Routing: **URL-rewrite** (`/resident/dashboard` via .htaccess)
- Appointments: **Full booking system** (residents select dates/times)
- Notifications: **Skip for MVP** (can add later)

---

## **Days 1-2: Foundation & Database Setup**

### **Day 1: Database Schema & Configuration**

**Files to Create/Modify:**
1. `config/database.php` - PDO connection with error handling
2. `sql/schema.sql` - Full database schema (users, certificates, requests, appointments)
3. `.htaccess` - URL rewriting rules for clean routing

**Tasks:**
- Create MySQL tables: `users`, `certificates`, `requests`, `appointments`
- Set up PDO connection with proper error modes
- Create helper functions for database transactions
- Write .htaccess for URL rewriting (`/resident/dashboard` → `public/index.php?route=resident/dashboard`)

**Deliverables:**
- Functional PDO connection tested
- Database schema with relationships defined
- URL rewriting configured

---

### **Day 2: Base Models & Database Helpers**

**Files to Create:**
1. `models/User.php` - User CRUD, password hashing, role methods
2. `models/Certificate.php` - Certificate CRUD
3. `models/Request.php` - Request CRUD with status workflow
4. `config/helpers.php` - Session helpers, validation, escaping functions

**Tasks:**
- Implement `User::create()`, `User::findById()`, `User::findByEmail()`, `User::validateLogin()`
- Implement `Certificate::getAll()`, `Certificate::create()`, `Certificate::update()`, `Certificate::delete()`
- Implement `Request::create()`, `Request::findById()`, `Request::findByUserId()`, `Request::updateStatus()`
- Create helper functions: `startSession()`, `isLoggedIn()`, `isAdmin()`, `validateEmail()`, `sanitize()`, `escape()`

**Deliverables:**
- All models follow PSR-like conventions
- Prepared statements used throughout
- Helper functions for common operations

---

## **Days 3-5: Authentication & Role-Based Dashboards**

### **Day 3: Authentication System**

**Files to Create/Modify:**
1. `controllers/AuthController.php` - login, register, logout methods
2. `views/auth/login.php` - Login form with Tailwind styling
3. `views/auth/register.php` - Registration form (resident only)
4. `public/index.php` - Main router/entry point

**Tasks:**
- Implement user registration with validation and `password_hash()`
- Implement login with session creation
- Implement logout with session destruction
- Create main router in `index.php` that routes based on URL
- Add session middleware/guard for protected routes

**Deliverables:**
- Residents can register and login securely
- Sessions work correctly
- Router directs to correct pages

---

### **Day 4: Resident Dashboard**

**Files to Create/Modify:**
1. `views/resident/dashboard.php` - Overview of resident requests, quick stats
2. `controllers/RequestController.php` - Methods for resident request viewing
3. `views/layout/header.php` - Navigation with role-based links, user logout
4. `views/layout/footer.php` - Basic footer

**Tasks:**
- Display resident's current requests with status badges
- Show quick stats: Pending, Approved, Completed counts
- Add "Create Request" button
- Create reusable header/footer with Tailwind nav

**Deliverables:**
- Resident dashboard displays their requests
- Navigation works correctly
- Session protection prevents unauthorized access

---

### **Day 5: Admin Dashboard**

**Files to Create/Modify:**
1. `controllers/AdminController.php` - Methods for viewing all requests, stats
2. `views/admin/dashboard.php` - Admin overview with request counts, pending list
3. Update `public/index.php` - Add admin route guards

**Tasks:**
- Display all requests across all residents
- Show summary stats: Total, Pending, Approved, Completed, Rejected
- Add quick action buttons for pending requests
- Implement route protection (only admins can access `/admin/*`)

**Deliverables:**
- Admin dashboard displays system-wide metrics
- Route guards working (non-admins cannot access admin pages)
- Admin can see all requests

---

## **Days 6-8: Certificate & Request Management**

### **Day 6: Certificate Management (Admin)**

**Files to Create/Modify:**
1. `views/admin/manage-certificate.php` - List, add, edit, delete certificates
2. Update `AdminController.php` - Add certificate CRUD methods
3. Create `public/assets/css/tailwind.css` (or link CDN)

**Tasks:**
- Implement `AdminController::getAllCertificates()`
- Implement `AdminController::createCertificate()` with POST handling
- Implement `AdminController::updateCertificate()` and `deleteCertificate()`
- Create dynamic form for certificate addition
- Display certificate list with edit/delete buttons

**Deliverables:**
- Admins can CRUD certificate types (Residency, Indigency, etc.)
- Forms include validation and error messages
- Certificates stored and retrieved from database

---

### **Day 7: Request Submission & Appointment Booking**

**Files to Create/Modify:**
1. `views/resident/create-request.php` - Multi-step form: select certificate type, details, appointment date
2. Update `RequestController.php` - Methods for creating requests, fetching available slots
3. Create `models/Appointment.php` - Methods for checking availability, creating appointments

**Tasks:**
- Create form for residents to select certificate type
- Add appointment booking widget (show available dates/times)
- Implement `RequestController::createRequest()` with validation
- Implement `Appointment::getAvailableSlots()` and `Appointment::bookSlot()`
- Store request with status = "Pending"

**Deliverables:**
- Residents can submit certificate requests
- Appointment dates are selectable and validated
- Requests appear in their dashboard

---

### **Day 8: Request Management (Admin Actions)**

**Files to Create/Modify:**
1. `views/admin/manage-request.php` - View all requests with filters, action buttons
2. Update `AdminController.php` - Methods for approving, rejecting, completing requests
3. Add remarks/notes system

**Tasks:**
- Display all requests in table format (filterable by status)
- Implement approve/reject/complete buttons
- Add remarks field for admin feedback
- Update request status in database
- Show resident details alongside request

**Deliverables:**
- Admins can approve, reject, or mark requests as completed
- Residents see status updates in their dashboard
- Admin can add remarks to requests

---

## **Days 9-10: Polish & Testing**

### **Day 9: Views & UX Refinement**

**Files to Create/Modify:**
1. `views/resident/my-request.php` - Detailed request view with appointment, status, remarks
2. Add error/success message templates
3. Create 404 and error pages
4. Enhance all forms with validation messages

**Tasks:**
- Build detailed request view page
- Add flash message system for success/error alerts
- Implement form validation on client-side (Tailwind + vanilla JS) and server-side
- Add pagination for request lists
- Improve responsive design with Tailwind

**Deliverables:**
- All views are polished and user-friendly
- Error handling is clear and helpful
- Mobile-responsive design verified

---

### **Day 10: Security, Testing & Documentation**

**Files to Create/Modify:**
1. `README.md` - Complete setup instructions
2. `sql/sample-data.sql` - Test data for development
3. Update all files with security improvements
4. Create `.env.example` for configuration

**Tasks:**
- Add CSRF token protection to all forms
- Implement rate limiting on login
- Verify SQL injection prevention (PDO prepared statements)
- Test password hashing and session security
- Write README with installation steps
- Create sample data for testing
- Performance testing and optimization

**Deliverables:**
- System is secure and production-ready
- All features tested and working
- Documentation complete

---

## **File Task Matrix (Day-by-Day Breakdown)**

| Day | Primary Files | Key Features |
|-----|---------------|--------------|
| **1** | `config/database.php`, `sql/schema.sql`, `.htaccess` | Database setup, URL rewriting |
| **2** | `models/*`, `config/helpers.php` | Model layer, database helpers |
| **3** | `AuthController`, `views/auth/*`, `public/index.php` | Registration, login, routing |
| **4** | `views/resident/dashboard.php`, `views/layout/*` | Resident dashboard, navigation |
| **5** | `AdminController`, `views/admin/dashboard.php` | Admin dashboard, stats |
| **6** | `views/admin/manage-certificate.php` | Certificate CRUD |
| **7** | `views/resident/create-request.php`, `Appointment.php` | Request submission, appointment booking |
| **8** | `views/admin/manage-request.php` | Request management, approval workflow |
| **9** | `views/resident/my-request.php`, error pages | View refinement, polish |
| **10** | `README.md`, `sql/sample-data.sql` | Security, testing, documentation |

---

## **Verification Checklist**

- [ ] Database connection works with sample query
- [ ] User registration/login flow works end-to-end
- [ ] Session persistence across page reloads
- [ ] Role-based access control blocking non-admins
- [ ] Residents can create and track requests
- [ ] Admins can approve/reject with remarks
- [ ] Appointment booking shows available slots
- [ ] All forms have validation (client + server)
- [ ] PDO prepared statements used everywhere
- [ ] Passwords hashed with `password_hash()`
- [ ] CSRF tokens on all forms
- [ ] Mobile responsiveness verified
