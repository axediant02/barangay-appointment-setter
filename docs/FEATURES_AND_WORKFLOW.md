# Barangay Certificate Request System — Features, Logic & Workflow

**Purpose:** Reference for demo and final presentation (stakeholders and technical audience).  
**Last updated:** For study and presentation prep.

---

## 1. Project Overview

### 1.1 What It Is

A **digital certificate request and appointment management system** for a barangay (local government unit in the Philippines). It lets:

- **Residents** request official documents (e.g. Barangay Clearance, Indigency Certificate), set an appointment date, and track status.
- **Admins** review requests, approve/reject/complete them, add remarks, and manage the list of certificate types.

### 1.2 High-Level Value

- **Residents:** Request and track certificates online; fewer trips and long queues.
- **Barangay:** Centralized requests, clear status workflow, and audit trail (remarks, status changes).
- **Stakeholders:** Transparency, basic analytics (counts by status), and a single system for document types and requests.

### 1.3 Tech Stack

| Layer        | Technology / Approach |
|-------------|------------------------|
| Backend     | PHP (procedural + OOP), no framework |
| Data        | MySQL, PDO with prepared statements |
| Frontend    | HTML, Tailwind CSS, minimal JavaScript (fetch for sync) |
| Auth        | Session-based; roles: `resident`, `admin` |
| Entry       | Single front controller: `public/index.php` + `?page=` routing |
| API         | `public/api.php` — JSON, action-based (`?action=`) |

---

## 2. Application Entry & Routing

### 2.1 How a Request Is Handled

1. User opens a URL (e.g. `https://yoursite.com/public/?page=my-requests`).
2. Web server points to `public/index.php` (or equivalent).
3. `index.php` runs first:
   - `session_start()`
   - `require_once '../config/database.php'` → `$pdo` and DB connection
   - `$isLoggedIn = isset($_SESSION['user_id'])`
   - `$role = $_SESSION['role'] ?? null`
   - `$page = $_GET['page'] ?? 'home'`
4. A `switch ($page)` routes to the right case: include a view, or instantiate a controller and call a method.
5. For protected pages, the route checks `$isLoggedIn` and `$role`; if not allowed, it redirects (e.g. to `?page=login`).

### 2.2 Route Map (index.php)

| `?page=` value       | Who can access | What happens |
|----------------------|----------------|-------------|
| `home` (default)     | Anyone         | If logged in → redirect to role dashboard; else → landing page. |
| `login`              | Anyone         | If logged in → redirect to role dashboard; else → show login form. |
| `register`           | Anyone         | If logged in → redirect to resident dashboard; else → show register form. |
| `logout`             | Anyone         | Clear session, redirect to `?page=home`. |
| `resident-dashboard`| Resident only  | Include resident dashboard view. |
| `create-request`     | Resident only  | GET: show form (RequestController::createForm); POST: save (RequestController::store). |
| `my-requests`        | Resident only  | RequestController::myRequests() → list resident’s requests (paginated). |
| `view-request`       | Resident only  | RequestController::viewRequest() → show one request (by id, must belong to user). |
| `edit-request`       | Resident only  | RequestController::editRequest() → show/edit form (only Pending). |
| `cancel-request`     | Resident only  | POST: RequestController::cancel(); GET → redirect to my-requests. |
| `admin-dashboard`    | Admin only     | Include admin dashboard view. |
| `manage-requests`    | Admin only     | GET: list all requests; POST: update status/remarks (AdminController). |
| `manage-certificates`| Admin only     | List certificates; add (POST); delete via GET param. |

**Logic rule:** Before any resident route, `index.php` checks `!$isLoggedIn || $role !== 'resident'` → redirect to `?page=login`. Before any admin route, it checks `!$isLoggedIn || $role !== 'admin'` → redirect to `?page=login`.

---

## 3. Authentication

### 3.1 Registration (`?page=register`)

**Flow:**

1. User submits form: username, email, password, confirm password.
2. Validation:
   - All fields required.
   - Passwords must match.
   - Password length ≥ 6.
   - Email must not already exist in `users`.
3. On success:
   - `password_hash($password, PASSWORD_DEFAULT)`.
   - Insert into `users` with `role = 'resident'`.
   - Set `$_SESSION['user_id']`, `$_SESSION['role']`, `$_SESSION['username']`.
   - `session_write_close()` then redirect to `?page=resident-dashboard`.

**Logic:** Registration always creates a **resident**. There is no self-service admin signup.

### 3.2 Login (`?page=login`)

**Flow:**

1. User submits email and password.
2. Look up user by email; verify with `password_verify($password, $user['password'])`.
3. On success:
   - Set `$_SESSION['user_id']`, `$_SESSION['role']`, `$_SESSION['username']`, `$_SESSION['email']`.
   - `session_commit()` then redirect by role:
     - `admin` → `?page=admin-dashboard`
     - else → `?page=resident-dashboard`.

**Logic:** One session per user; role determines landing page and which routes/API actions are allowed.

### 3.3 Logout (`?page=logout`)

- `session_unset()` and `session_destroy()`.
- Redirect to `?page=home`.

### 3.4 Home / Default (`?page=home` or no `page`)

- If not logged in → show **landing page** (no dashboard).
- If logged in:
  - Admin → redirect to `?page=admin-dashboard`.
  - Resident → redirect to `?page=resident-dashboard`.

This avoids showing login/register to already-logged-in users and sends them straight to the right dashboard.

---

## 4. Resident Features

### 4.1 Resident Dashboard (`?page=resident-dashboard`)

**Purpose:** Summary of the resident’s request activity and quick actions.

**Data loaded (server-side):**

- Current user’s name from `users`.
- Counts: total, pending, approved, completed, rejected (from `requests` for this `user_id`).
- Last 5 requests (same user) with certificate name.

**UI:**

- Stat cards: Total, Pending, Approved, Completed, Rejected.
- List of recent requests with status badges and links to view.
- “New Request” button → `?page=create-request`.

**Real-time sync (JavaScript):**

- Every 20 seconds, `fetch(APP_BASE + 'api.php?action=resident-stats')`.
- Response JSON: `{ total, pending, approved, completed, rejected }`.
- Stats on the page are updated without full reload.

**Logic:** All queries are scoped by `$_SESSION['user_id']`. No other resident’s data is shown.

---

### 4.2 Create Request (`?page=create-request`)

**Purpose:** Submit a new certificate request with appointment date and personal details.

**Form (GET – createForm):**

- List of **certificate types** from `certificates` (Certificate model).
- Pre-fill: username/email from `users` for the logged-in user.
- **Business rules enforced before showing form:**
  - For each certificate type, count how many **non-cancelled** requests the user has **today** for that certificate. If ≥ 1, that certificate is not offered (or marked as “already requested today”).
  - Count how many times the user has **cancelled** requests for each certificate type. If ≥ 3 for a type, that type is **temporarily blocked** for new requests (cancellation limit).

**Store (POST – store):**

1. **Required fields:** certificate_id, appointment_date, full_name, address, contact_number. Optional: civil_status, birthday.
2. **Cancellation limit:** If the user already has 3 cancellations for this certificate type → set `$_SESSION['error']`, redirect back to create form.
3. **One active request per certificate per day:** `RequestModel::canCreateRequest($userId, $certificateId)` checks:
   - Not banned (fewer than 3 cancellations for this certificate).
   - No existing non-cancelled request for this user + certificate + today.
4. Insert into `requests`: user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number. Status is set as **Pending** (default in DB or in insert).
5. Success message in session, redirect to `?page=my-requests`.

**Logic summary:**

- At most **one active request per certificate per day** per resident.
- After **3 cancellations** for a certificate type, that type is blocked for new requests (anti-abuse).

---

### 4.3 My Requests (`?page=my-requests`)

**Purpose:** Paginated list of the resident’s requests with status and real-time updates.

**Data (RequestController::myRequests):**

- Pagination: 5 per page (`page_num` in GET).
- Count total requests for user; compute total pages.
- Load page of requests with certificate name (JOIN `certificates`).
- For each certificate type, count user’s cancellations; if ≥ 3, mark as “banned” for that type (used in UI, e.g. disable “Request again” or show message).

**UI:**

- Cards per request: certificate name, status, dates, link to view, and (if Pending) cancel/edit actions.
- Pagination controls.
- **Real-time sync (JavaScript):** Every 5 seconds, `fetch(APP_BASE + 'api.php?action=my-requests')`. Response is JSON array of requests. Script updates existing request cards (e.g. status, remarks) so the list reflects admin updates without refresh.

**Logic:** All data filtered by `user_id = $_SESSION['user_id']`.

---

### 4.4 View Request (`?page=view-request&id=...`)

**Purpose:** See full details of one request.

**Data:**

- `RequestModel::findByIdAndUser($id, $userId)` → one row only if `id` matches and `user_id` matches. Otherwise 404 / redirect to my-requests with error.

**UI:**

- Certificate name, status, appointment date, personal info, contact, admin remarks (if any).
- If status is **Pending**, show links to “Edit” and “Cancel”.

**Logic:** Residents can only view their own requests.

---

### 4.5 Edit Request (`?page=edit-request&id=...`)

**Purpose:** Update details of a **Pending** request only.

**Data:**

- Same as view: `findByIdAndUser($id, $userId)`. If not found, redirect to my-requests with error.
- If `status !== 'Pending'`, the view shows a message that the request cannot be edited (and link back to view).

**Update (POST):**

- Validate: full_name, address, contact_number, appointment_date required.
- `RequestModel::updateResidentRequest(...)` updates: full_name, civil_status, birthday, address, contact_number, appointment_date.  
  **Condition in SQL:** `id = ? AND user_id = ? AND status = 'Pending'`. So only the owner and only Pending requests can be updated.
- On success → redirect to `?page=view-request&id=...` with success message.

**Logic:** No status change here; only resident-editable fields. Admin-side status changes are in Manage Requests.

---

### 4.6 Cancel Request (`?page=cancel-request`)

**Purpose:** Resident cancels a **Pending** request (with cancellation limit).

**Flow (POST only):**

1. `request_id` from POST.
2. Load request: must be same user and status = **Pending**. Else error, redirect to my-requests.
3. **Cancellation limit:** `RequestModel::canCancelRequest($userId, $certificateId)` — true only if user has fewer than 3 cancellations for that certificate type. If already 3 → error, redirect to my-requests.
4. UPDATE `requests` SET `status = 'Cancelled'` WHERE `id` and `user_id` and `status = 'Pending'`.
5. Success message, redirect to my-requests.

**Logic:** Only Pending can be cancelled. After 3 cancellations per certificate type, the user cannot cancel more for that type (and cannot create new ones for that type until logic is relaxed or reset).

---

## 5. Admin Features

### 5.1 Admin Dashboard (`?page=admin-dashboard`)

**Purpose:** System-wide overview for admins.

**Data:**

- Total residents: `COUNT(*)` from `users` WHERE `role = 'resident'`.
- Request counts: total, pending, approved, completed, rejected (from `requests`).
- Recent 8 requests: JOIN `requests`, `users`, `certificates`; show resident name, certificate name, status, date.

**UI:**

- Stat cards and table/list of recent requests.
- Links to Manage Requests and Manage Certificates.
- Can add periodic sync via API (e.g. `admin-dashboard-sync`) to refresh stats without reload.

**Logic:** No resident data filtering; admin sees all requests and all residents.

---

### 5.2 Manage Requests (`?page=manage-requests`)

**Purpose:** List all requests and update status + remarks.

**Data (AdminController::manageRequests):**

- `RequestModel::getAll()` — all requests with resident username and certificate name.
- Pagination in view: 25 per page, `page_num` in GET; slice array in controller (or could be done in model).

**UI:**

- Table: resident name, contact, certificate, current status, remarks field, status dropdown, “Update” button.
- Status dropdown options depend on **current status** (see Status Workflow below).
- Search/filter (client-side in the view) by resident/certificate text.

**Update (POST – AdminController::updateRequest):**

- `request_id`, `status`, `remarks` from POST.
- `RequestModel::updateStatus($id, $status, $remarks)`:
  - Load current request (id must exist).
  - **Allowed transitions:**
    - **Pending** → Approved, Rejected, or Cancelled (Cancelled can also be set by resident).
    - **Approved** → Completed.
    - **Rejected / Completed / Cancelled** → no status change (only remarks can be updated).
  - If transition is invalid, return false → session error. If valid, UPDATE status and remarks.
- Redirect back to `?page=manage-requests`.

**Logic:** Admin can only move status along the defined workflow. Remarks are stored for audit/feedback.

---

### 5.3 Manage Certificates (`?page=manage-certificates`)

**Purpose:** CRUD for certificate types (what residents can request).

**List:**

- `certificates` table: id, name, description. Sorted by name. Rendered as cards with search (client-side).

**Add (POST in same page):**

- `name`, `description` required. INSERT into `certificates`. Redirect to same page.

**Delete:**

- Link: `?page=manage-certificates&delete=1&id=...`. Confirm dialog in JS. DELETE FROM certificates WHERE id = ?. Redirect back.

**Logic:** Deleting a certificate does not delete old requests (they keep certificate_id); new requests can no longer use that type. No soft-delete in the codebase.

---

## 6. Request Status Workflow

### 6.1 Statuses

- **Pending** — Just submitted; resident can edit/cancel; admin can approve or reject.
- **Approved** — Admin approved; resident can no longer edit/cancel; admin can mark **Completed**.
- **Completed** — Document issued; terminal.
- **Rejected** — Admin rejected; terminal (with optional remarks).
- **Cancelled** — Resident (or possibly admin) cancelled; terminal.

### 6.2 Allowed Transitions (RequestModel::updateStatus)

| Current   | Allowed next      |
|----------|-------------------|
| Pending  | Approved, Rejected, Cancelled |
| Approved | Completed         |
| Rejected | (none)            |
| Completed| (none)            |
| Cancelled| (none)            |

If the same status is sent, only **remarks** are updated. Any other transition is rejected (returns false).

---

## 7. REST/JSON API (api.php)

**Purpose:** Support real-time sync and future front-ends without full page reloads.

**General:**

- All responses JSON. `Content-Type: application/json`.
- `session_start()` and require login: if no `$_SESSION['user_id']`, respond 401 Unauthorized.
- Action via `$_GET['action']`. Invalid or missing action → 400.
- Role checks: resident actions require `$_SESSION['role'] === 'resident'`, admin actions require `admin`. Else 403 Forbidden.
- Exceptions → 500 with error message in JSON.

### 7.1 Resident API Actions

| Action            | Method | Description | Response |
|-------------------|--------|-------------|----------|
| `resident-stats`  | GET    | Counts for current user’s requests (total, pending, approved, completed, rejected). | `{ total, pending, approved, completed, rejected }` |
| `my-requests`     | GET    | All requests for current user with certificate name. | Array of request objects. |
| `cancel-request`  | POST   | Cancel a request (same rules as page: Pending, ownership, cancellation limit). | Used by form/redirect flow. |

### 7.2 Admin API Actions

| Action                  | Method | Description | Response |
|-------------------------|--------|-------------|----------|
| `admin-stats`           | GET    | System-wide: resident count + request counts by status. | Object with residents + counts. |
| `admin-recent-requests` | GET    | Last 8 requests with resident name, certificate name, formatted date. | Array of objects. |
| `manage-requests`       | GET    | All requests (for admin UI or export). | Array of request objects. |
| `admin-requests`        | GET    | Paginated (page_num, 25 per page); dates formatted. | Array of request objects. |
| `admin-dashboard-sync` | GET    | Stats for dashboard: residents count + request counts. | `{ stats: { residents, total, pending, ... } }` |

**Logic:** Same as in pages — resident actions filter by `user_id`; admin actions see all data. Front-end uses `APP_BASE` (set in layout) so the correct `api.php` path is used even in subdirectories.

---

## 8. Database & Configuration

### 8.1 Database Config (`config/database.php`)

- Reads: `MYSQLHOST`/`DB_HOST`, `MYSQLPORT`/`DB_PORT`, `MYSQLDATABASE`/`DB_NAME`, `MYSQLUSER`/`DB_USER`, `MYSQLPASSWORD`/`DB_PASSWORD` (env vars for production).
- Fallback: localhost, 3306, `barangay-appointment`, root, empty.
- PDO: exception mode, associative fetch, no emulated prepares. On connection failure: 503 and short HTML message.

### 8.2 Main Tables (conceptual)

- **users** — id, username (or name), email, password (hashed), role (`resident`|`admin`).
- **certificates** — id, name, description.
- **requests** — id, user_id, certificate_id, appointment_date, full_name, civil_status, birthday, address, contact_number, status, remarks, created_at (and any other columns in your schema).

Relations: requests.user_id → users.id; requests.certificate_id → certificates.id.

---

## 9. Security & Access Control Summary

- **Authentication:** Session-based; `user_id` and `role` in `$_SESSION` after login.
- **Authorization:** Every protected route in `index.php` checks role; views/controllers re-check where needed (e.g. resident dashboard, view request by id).
- **Resident data:** All resident-facing queries use `$_SESSION['user_id']`. Residents cannot see or change others’ requests.
- **Admin data:** Admin-only routes and API actions; no resident can call admin actions (403 if tried).
- **Passwords:** Stored with `password_hash(PASSWORD_DEFAULT)`; verified with `password_verify`.
- **Input:** Prepared statements for DB; output escaped (e.g. `htmlspecialchars`) in views where applicable.

---

## 10. Demo & Presentation Flow (Suggested)

### 10.1 For Stakeholders (business)

1. **Problem:** Long queues, paper-based requests, no visibility.
2. **Solution:** One system: request online, set appointment, track status.
3. **Resident flow:** Register → Login → Dashboard → New Request (pick certificate, date, details) → My Requests (see status, optional edit/cancel) → View request (see remarks when approved/rejected).
4. **Admin flow:** Login → Dashboard (overview) → Manage Requests (approve/reject/complete, add remarks) → Manage Certificates (add/remove document types).
5. **Rules:** One request per certificate per day; 3 cancellations per certificate type then blocked; clear status workflow.
6. **Benefits:** Fewer trips, faster processing, transparency, simple reporting (counts).

### 10.2 For Technical Audience

1. **Architecture:** Single entry point (`index.php`), `?page=` routing, role-based redirects.
2. **Layers:** Config → Models (Request, Certificate) → Controllers (RequestController, AdminController) → Views; API separate (`api.php`) for JSON.
3. **Auth:** Session; role in session; every protected route checks before include/controller.
4. **Business logic:** In controllers and models (e.g. `canCreateRequest`, `canCancelRequest`, `updateStatus` transitions).
5. **Data:** PDO, prepared statements, scoped queries (user_id for resident, no scope for admin).
6. **Real-time:** Polling via API (resident-stats, my-requests) with `APP_BASE` for correct URL.

---

## 11. File Map (Quick Reference)

| Path | Purpose |
|------|--------|
| `public/index.php` | Front controller; routing; auth checks. |
| `public/api.php` | JSON API; session + role checks; action switch. |
| `config/database.php` | PDO connection. |
| `controllers/RequestController.php` | createForm, store, myRequests, viewRequest, editRequest, cancel. |
| `controllers/AdminController.php` | manageRequests, updateRequest, manageCertificates (create/delete in view). |
| `models/Request.php` (RequestModel) | CRUD, findByIdAndUser, updateStatus, canCreateRequest, canCancelRequest, counts. |
| `models/Certificate.php` | getAll. |
| `views/landing-page.php` | Home for guests. |
| `views/auth/login.php`, `register.php` | Auth forms and processing. |
| `views/resident/dashboard.php`, `create-request.php`, `my-request.php`, `view-request.php`, `edit-request.php` | Resident UI. |
| `views/admin/dashboard.php`, `manage-request.php`, `manage-certificate.php` | Admin UI. |
| `views/layout/header.php`, `footer.php` | Shared layout; header sets `APP_BASE` and nav by role. |

Use this document to walk through every feature, explain the logic and workflow behind each screen and API, and prepare for both stakeholder demos and technical Q&A.
