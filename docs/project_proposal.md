# Project Proposal: Barangay Appointment Setter

## 1. Purpose
The purpose of this system is to digitalize and streamline the process of requesting barangay-issued certificates (e.g., Clearance, Residency, Business Certificates). Currently, residents must visit barangay offices in person, wait in long queues, and rely on paper-based record-keeping, which is inefficient and error-prone.

This system will allow residents to:
- Submit certificate requests online with mandatory identity verification.
- Provide specific reasons for each request from a curated list.
- Select and schedule appointment dates.
- Track request status in real-time.

For barangay staff, the system will:
- Provide a centralized dashboard for managing requests with high information density.
- Enable streamlined ID verification and status updates (Pending → Approved → Completed/Rejected/Cancelled).
- Maintain resident traceability through contact info and email integration.
- Offer basic analytics to monitor workflow efficiency.

**Core Objective:** To improve efficiency, transparency, and accessibility in barangay certificate issuance through a modern, secure, and user-friendly digital platform.

## 2. Problem Statement
The current manual process has several challenges:
- **Inconvenience:** Long queues cause delays for residents.
- **Poor Traceability:** Paper-based logs make tracking and reporting difficult.
- **Status Uncertainty:** Lack of transparency creates uncertainty for residents.
- **Verification Gaps:** Manual ID checks on-site slowing down the issuance process.

## 3. Scope
### Included in the system:
- Resident registration and login with secure authentication.
- **Identity Verification:** Mandatory ID image upload for every individual request.
- **Reason-Based Requesting:** Residents select a specific reason (e.g., Employment, Education, Legal) for the certificate.
- Resident dashboard to view, edit (pending only), or cancel requests.
- **Enhanced Admin Dashboard:** 
    - Real-time search and pagination.
    - Quick-view Appointment column for scheduling.
    - Detailed Request Modal with resident bio, email, ID preview, and verification controls.
- Role-based access control.

### Excluded from the system:
- Integration with external government systems or e-signatures.
- Payment processing for certificate fees.
- Complex reporting or multi-level external approvals.

## 4. Users, Workflows, and Features
### Users:
- **Resident:** Submits requests with ID and reason, manages pending appointments, views history.
- **Admin:** Manages certificate types, performs ID verification, updates statuses, monitors metrics.

### Workflows:
1. **Request Submission:** Resident logs in, selects a certificate, uploads an ID, chooses a reason and appointment date.
2. **Review & Verification:** Admin views details in a consolidated modal, verifies the resident's ID, and updates the status to "Approved" or "Rejected".
3. **Completion:** Once the certificate is issued, the admin marks it as "Completed".
4. **Resident Tracking:** Resident monitors the status badge (Pending, Approved, etc.) in real-time.

### Key Features:
- **Request-Based ID Verification:** Secure image upload for identity/address proof per transaction.
- **Reason Categorization:** 10 pre-defined reasons to aid barangay record-keeping.
- **Premium Status Badges:** Color-coded, icon-based status tracking.
- **Resident Email Traceability:** Direct access to resident email for official communication.
- **Live Search & AJAX Filtering:** Instant request lookup on the admin dashboard.

## 5. System Structure and Technical Approach
- **Frontend:** HTML, TailwindCSS (Vanilla UI), Javascript (AJAX, Modal Logic).
- **Backend:** PHP (Vanilla) with an MVC-inspired structure.
- **Database:** MySQL (MariaDB) using PDO for secure, prepared SQL statements.
- **Architecture:** 
    - `controllers/` – Logic handling and routing.
    - `models/` – Database interaction (Requests, Users, Certificates).
    - `views/` – Clean, responsive UI templates for Admin and Resident.

## 6. Timeline
| Day | Task |
| :--- | :--- |
| 1 | Project setup, database design, authentication system |
| 2 | Resident registration/login, dashboard, request form |
| 3 | Admin dashboard, request management CRUD |
| 4 | Request editing and cancellation, validations |
| 5 | Status updates, analytics, error handling |
| 6 | UI/UX enhancements, responsive design |
| 7 | Testing, bug fixes, final deliverable |

## 7. Risks and Assumptions
- **Risks:** 
    - Data inconsistency if concurrent updates occur without proper locking.
    - Resident image storage management over time.
- **Assumptions:**
    - Admins have the legal authority to verify residents via digital ID uploads.
    - The system runs on a base web server (XAMPP) or similar stack.
