# Pro Academy — Project Summary

> **A full-stack Learning Management System (LMS)** built with a **Node.js/Express** backend and a **React (CRA)** frontend. It enables organizations to manage courses, quizzes, podcasts, webinars, prompt galleries, phishing awareness assessments, and user/team analytics through a role-based admin dashboard.

---

## 1. Project Overview

### Purpose
Pro Academy is an internal corporate learning platform. Employees can:
- Browse and enroll in courses organized by modules
- Take quizzes with timed exams and review past attempts
- Listen to podcasts with episodes, comments, and audio playback
- Watch webinars and download attached resources
- Explore an AI prompt gallery with role-based permissions
- Complete phishing awareness assessments
- Earn points, badges, and login bonuses
- Track progress toward course completion and certificates

Admins/super-admins can create content, manage teams/departments/sub-teams, view analytics and reports, manage video advertisements, and control user roles.

### Technology Stack

| Layer          | Technology                                                                 |
|----------------|---------------------------------------------------------------------------|
| **Frontend**   | React 18 (Create React App), Material UI (MUI) v5, React Router v6       |
| **Backend**    | Node.js, Express 4                                                       |
| **Database**   | MySQL (via `mysql` npm driver)                                            |
| **Styling**    | MUI + Emotion, custom CSS, dark/light mode & holiday themes               |
| **Charts**     | ApexCharts, Chart.js                                                      |
| **PDF/Media**  | jsPDF, html2canvas, Puppeteer (server-side), video.js, react-player       |
| **Auth**       | bcrypt password hashing, localStorage token, email OTP (nodemailer)       |
| **File Upload**| Multer (categorized storage with unique filenames)                        |

---

## 2. Folder / Component Structure

### Top-Level

```
pro-academy/
├── academy-server/          # Backend (Express API server)
└── pro-academy-2.0/         # Frontend (React SPA)
```

### Backend — `academy-server/`

| Folder/File      | Purpose                                                                |
|-------------------|------------------------------------------------------------------------|
| `app.js`          | Express entry point — CORS, JSON parsing, static files, route mounting |
| `dbConfig.js`     | MySQL connection using env vars                                        |
| `.env`            | Config: `PORT`, `HOST`, `USER`, `PASSWORD`, `DATABASE`                 |
| `handlers/`       | Express routers split by CRUD operation                                |
| ├── `createHandlers.js` | POST routes: courses, modules, quizzes, users, podcasts, episodes, webinars, prompts, phishing, ads, bookmarks, comments, email auth, file uploads |
| ├── `readHandler.js`    | GET/POST routes: fetch courses, modules, users, progress, exams, bookmarks, teams, categories, podcasts, webinars, prompts, badges, phishing, points, ads, login history |
| ├── `updateHandler.js`  | POST/PUT routes: update courses, modules, quizzes, profiles, users, episode media, ads, teams, archive/unarchive, phishing questions |
| └── `deleteHandler.js`  | DELETE routes: courses, bookmarks, modules, teams, sub-teams, ads, episodes, podcasts, prompts, phishing questions |
| `services/`       | Business logic classes (SQL queries)                                   |
| ├── `createServices.js`  | Insert operations for all entities                                |
| ├── `readService.js`     | **Largest file (2050 lines, 77 methods)** — all SELECT queries, login/logout, login bonus logic, phishing assessment queries |
| ├── `updateService.js`   | UPDATE operations for all entities                                |
| └── `deleteService.js`   | DELETE operations for all entities                                |
| `utils/upload.js` | Multer config: storage destinations by type (podcast, episode, webinar, prompt), file filters (PDF, image), unique filename generation |
| `database/`       | SQL schema/seed files for login bonus and phishing assessment tables    |
| `public/`         | Static file serving (uploaded materials)                               |

### Frontend — `pro-academy-2.0/src/`

| Folder            | Purpose                                                                |
|---------------------|------------------------------------------------------------------------|
| `App.js`            | Root component — context providers (Theme, Audio, Video), routing      |
| `routes.js`         | All route definitions with auth guards                                 |
| `config.js`         | `baseUrl` for API calls (default: `http://localhost:8000`)             |
| `pages/` (41 files) | Page-level components (see major pages below)                         |
| `sections/@dashboard/` | Dashboard section components: `app/`, `courses/`, `podcast/`, `prompt/`, `toolkits/`, `user/`, `webinar/` |
| `components/` (17 dirs) | Reusable UI: `audio-player`, `video-player`, `chart`, `modal`, `iconify`, `label`, `nav-section`, `scrollbar`, `particles`, `rotating-text`, `celebration`, `custom-cursor`, `back-to-top`, `scroll-to-top`, `logo`, `svg-color`, `color-utils` |
| `middleware/` (9 dirs) | Feature-specific logic: `contentCourse`, `courses`, `imageInput`, `loaders`, `loginBonus`, `modal`, `quiz`, `user`, `videoInput` |
| `layouts/`          | `dashboard/` (sidebar nav, header) and `simple/` (minimal layout)      |
| `store/`            | `ThemeDarkMode.js` — React Context for light/dark/custom themes        |
| `utils/`            | Helpers: `permissionManager.js` (RBAC), `cssStyles.js`, `formatNumber.js`, `formatTime.js`, `mediaUtils.js`, `videoDuration.js`, `extractColorFromImage.js`, `christmasAnimation.js`, `halloweenAnimation.js`, `sidebarThemeOverride.js`, `themeBackgroundOverride.js` |
| `theme/`            | MUI theme customization (17 files)                                     |
| `hooks/`            | Custom React hooks                                                     |
| `_mock/`            | Mock data (9 files)                                                    |
| `styles/`           | Global CSS (3 files)                                                   |

### Major Pages

| Page Component                 | Description                                           |
|--------------------------------|-------------------------------------------------------|
| `DashboardAppPage.js`          | Main dashboard with analytics widgets                 |
| `CoursePage.js`                | Browse/search courses with filtering                  |
| `CourseOverview.js`            | Course detail view with modules list                  |
| `CourseContentPage.js`         | Module content viewer                                 |
| `CourseEditPage.js`            | Admin: edit course details and modules (78 KB)        |
| `CourseAddModulePage.js`       | Admin: add new modules to courses                     |
| `CourseCompletedPage.js`       | Course completion screen with certificates             |
| `quizPage.js`                  | Quiz/exam interface                                   |
| `AddQuizPage.js` / `ViewQuizPage.js` | Admin: create and view quizzes                 |
| `Podcast.js`                   | Podcast listing page                                  |
| `PodcastPage.js`               | Podcast detail with episodes list (39 KB)             |
| `EpisodePage.js`               | Episode player with comments                          |
| `WebinarLibrary.js`            | Webinar listing and management (34 KB)                |
| `PromptGallery.js`             | AI prompt gallery with RBAC                           |
| `Reports.js`                   | Admin analytics and reports (78 KB)                   |
| `Settings.js`                  | Admin: team/ad management (34 KB)                     |
| `TeamRecords.js` / `TeamCourses.js` | Admin: team and course analytics              |
| `UserPage.js`                  | Admin: manage users table                             |
| `UserProfile.js`               | User profile with edit capability (39 KB)             |
| `UserProfileAdmin.js`          | Admin view of user profiles                           |
| `LoginPage.js` / `RegisterPage.js` | Authentication pages                            |
| `BadgesSection.js`             | Badges/achievements display                           |
| `LevelCard.js`                 | User level/progress card                              |

---

## 3. API Usage

### Base URL
- Development: `http://localhost:8000`
- Production: `https://academy.proweaver.tools/datatemple/api` (commented out in `config.js`)

### Internal API Endpoints

All API calls are made via **Axios** from the frontend to the Express backend. There is **no API versioning** or authentication middleware on the server — routes are open.

#### Create (POST)

| Endpoint                    | Purpose                                    |
|-----------------------------|--------------------------------------------|
| `POST /insertData`          | Insert generic course/module data          |
| `POST /insertContentmodule` | Insert module content                      |
| `POST /AddCourse`           | Create a new course                        |
| `POST /examLogs`            | Log quiz/exam attempt and results          |
| `POST /register`            | Register a new user (bcrypt hashing)       |
| `POST /authEmail`           | Send email OTP via nodemailer              |
| `POST /Addquiz`             | Add a quiz to a module                     |
| `POST /uploadContent`       | Upload a PDF file                          |
| `POST /uploadFiles`         | Upload multiple files (episode media)      |
| `POST /webinarUpload`       | Upload webinar assets (video, thumbnail, resources) |
| `POST /download`            | Download file to local storage             |
| `POST /addsubteam`          | Create a sub-team                          |
| `POST /addAdvertisement`    | Create a video advertisement               |
| `POST /podcast`             | Create a podcast                           |
| `POST /episode`             | Create a podcast episode with media        |
| `POST /addComment`          | Add an episode comment                     |
| `POST /addWebinar`          | Create a webinar                           |
| `POST /uploadPromptThumb`   | Upload prompt thumbnail                    |
| `POST /prompt`              | Create an AI prompt                        |
| `POST /startPhishingAssessment`  | Start a phishing assessment           |
| `POST /submitPhishingAnswer`     | Submit answer for phishing question   |
| `POST /completePhishingAssessment` | Mark assessment as complete         |
| `POST /createPhishingQuestion`   | Admin: create phishing question       |

#### Read (GET / POST)

| Endpoint                                   | Purpose                                       |
|--------------------------------------------|-----------------------------------------------|
| `GET /fetchData`                           | Fetch all courses                             |
| `GET /fetchSingleCourse/:id`              | Fetch single course detail                    |
| `GET /fetchModuleContent/:id`             | Fetch module content by ID                    |
| `GET /fetchModule/:id`                    | Fetch module by ID                            |
| `GET /fetchTeamCourse/:id`                | Fetch courses by team                          |
| `GET /fetchContent/:id`                   | Fetch content by ID                            |
| `GET /fetchProgress/:id`                  | Fetch user course progress                    |
| `GET /fetchCategory`                      | Fetch all categories                          |
| `GET /fetchAllTeams`                      | Fetch all teams                               |
| `GET /fetchCoursePerId/:id`               | Fetch course enrollees                        |
| `GET /fetchUserPerCourse`                 | Fetch user-course mapping                     |
| `GET /fetchEnrolleesPerCourse/:course_id` | Fetch enrollees for a course                  |
| `GET /fetchAds`                           | Fetch video advertisements                    |
| `GET /fetchLoginUser`                     | Fetch logged-in users                         |
| `GET /fetchPodcast`                       | Fetch all podcasts                            |
| `GET /fetchSelectedPodcast/:id`           | Fetch single podcast                          |
| `GET /fetchPodcastEpisode/:id`            | Fetch podcast episodes                        |
| `GET /fetchComment/:id`                   | Fetch episode comments                        |
| `GET /fetchPointsAll`                     | Fetch all user points                          |
| `GET /fetchPointsAllWithTeams`            | Fetch points with team info                   |
| `GET /fetchWebinar`                       | Fetch all webinars                            |
| `GET /fetchWebinarById/:id`               | Fetch single webinar                          |
| `GET /fetchBadges`                        | Fetch all badges                              |
| `GET /fetchPhishingQuestions`             | Fetch phishing questions                      |
| `GET /proxy-image?url=`                   | Proxy external images (CORS bypass)           |
| `POST /login`                             | User login (email + bcrypt password check)    |
| `POST /fetchAllCour`                      | Fetch all courses (with role filtering)       |
| `POST /fetchCurrentMod`                   | Fetch current module progress                 |
| `POST /fetchBookmark`                     | Fetch bookmarks                               |
| `POST /fetchPrompts`                      | Fetch all prompts                             |
| `POST /examHistoryLogs`                   | Fetch exam history                            |
| `POST /fetchAccomplishmentExamStatus`     | Fetch exam completion status                  |

#### Update (POST / PUT)

| Endpoint                          | Purpose                              |
|-----------------------------------|--------------------------------------|
| `POST /putCurrentMod`             | Update user's current module         |
| `POST /updateAccomplishmentExamStatus` | Update exam pass/fail status    |
| `POST /updateQuiz`                | Update quiz content                  |
| `POST /updateProfileAll`          | Update user profile fields           |
| `POST /archiveCourse/:id`         | Archive/unarchive a course           |
| `POST /updateUser/:id`            | Update user by ID                    |
| `POST /updateCourse/:id`          | Update course details                |
| `POST /updateTeam/:id`            | Update team info                     |
| `PUT /updateAdvertisement/:id`    | Update video ad                      |
| `PUT /updateEpisode/:id`          | Update episode with media            |
| `PUT /updateWebinar/:id`          | Update webinar details               |

#### Delete (DELETE)

| Endpoint                         | Purpose                              |
|----------------------------------|--------------------------------------|
| `DELETE /deleteCourse/:id`       | Delete a course                      |
| `DELETE /deleteBookmark/:id`     | Delete a bookmark                    |
| `DELETE /deleteModule/:contentId`| Delete module content                |
| `DELETE /deleteTeam/:id`         | Delete a team                        |
| `DELETE /deleteSubTeam/:id`      | Delete a sub-team                    |
| `DELETE /deleteAdvertisement/:id`| Delete a video ad                    |
| `DELETE /deleteEpisode/:id`      | Delete a podcast episode             |
| `DELETE /deletePodcast/:id`      | Delete a podcast                     |
| `DELETE /deletePrompt/:id`       | Delete a prompt                      |
| `DELETE /deletePhishingQuestion/:id` | Delete a phishing question       |

### External APIs
- **Image Proxy**: `GET /proxy-image?url=<external-url>` — fetches external images server-side to bypass CORS.
- **Nodemailer**: Sends OTP emails during registration (SMTP configured in `createServices.js`).

---

## 4. State Management / Data Flow

### Pattern: **API-driven with React Context**

```
┌─────────────┐     Axios      ┌──────────────┐       SQL       ┌──────────┐
│   React UI  │ ──────────────► │ Express API  │ ──────────────► │  MySQL   │
│  (Pages /   │ ◄────────json── │ (Handlers →  │ ◄────results─── │  (academy│
│  Sections)  │                 │  Services)   │                 │   db)    │
└─────────────┘                 └──────────────┘                 └──────────┘
```

### Frontend State

| Mechanism               | Usage                                                       |
|--------------------------|-------------------------------------------------------------|
| **React Context**        | `ThemeDarkModeCtxt` (light/dark/custom themes), `AudioPlayerContext` (global audio playback), `VideoPlayerContext` (global video playback) |
| **localStorage**         | Auth token (`token`), user data, custom theme preference     |
| **Component State**      | `useState` / `useEffect` for local page data (courses, users, etc.) |
| **Props Drilling**       | Parent-to-child data passing in sections/components          |

### Authentication Flow
1. User submits email + password on `LoginPage`
2. `POST /login` → server bcrypt-compares → returns user data
3. Frontend stores user data in `localStorage` (including `token`)
4. `routes.js` checks `localStorage.getItem("token")` for route protection
5. Logout clears localStorage and redirects

### Role-Based Access
- **Super Admin** (`status === 5`): Full CRUD on all content
- **Admin** (`status === 1`): CRUD on own content only
- **Regular User** (`status === 0`): View-only access
- Managed by `PromptPermissionManager` class in `utils/permissionManager.js`

---

## 5. Key Dependencies

### Backend (`academy-server`)

| Package       | Purpose                                        |
|---------------|------------------------------------------------|
| `express`     | HTTP server and routing                        |
| `mysql`       | MySQL database driver                          |
| `bcrypt`      | Password hashing                               |
| `cors`        | Cross-origin resource sharing                  |
| `multer`      | File upload handling (images, PDFs, audio, video)|
| `nodemailer`  | Email sending (OTP verification)               |
| `axios`       | HTTP client (image proxy)                      |
| `puppeteer`   | Server-side rendering (likely for PDF/certificate generation) |
| `dotenv`      | Environment variable loading                   |
| `nodemon`     | Auto-restart in development                    |

### Frontend (`pro-academy-2.0`)

| Package                    | Purpose                                         |
|----------------------------|-------------------------------------------------|
| `@mui/material` + `@mui/lab` + `@mui/icons-material` | UI component library |
| `@mui/x-data-grid`        | Data table component                            |
| `@mui/x-date-pickers`     | Date picker components                          |
| `react-router-dom`        | Client-side routing                             |
| `axios`                   | HTTP client for API calls                       |
| `apexcharts` + `react-apexcharts` | Dashboard charts                       |
| `chart.js` + `react-chartjs-2`    | Additional charting                    |
| `jspdf` + `html2canvas`   | Client-side PDF generation                      |
| `react-hook-form`         | Form handling                                   |
| `react-toastify`          | Toast notifications                             |
| `react-player`            | Video playback                                  |
| `video.js`                | Advanced video player                           |
| `react-slick` + `slick-carousel`  | Carousels/sliders                      |
| `react-confetti-explosion`| Celebration animations                          |
| `crypto-js`               | Client-side encryption                          |
| `js-cookie`               | Cookie management                               |
| `date-fns` + `dayjs` + `moment` | Date manipulation (3 redundant libraries!) |
| `lodash`                  | Utility functions                               |
| `@iconify/react`          | Icon library                                    |
| `antd`                    | Ant Design components (supplementary)           |
| `react-helmet-async`      | SEO / document head management                  |
| `@ffmpeg/ffmpeg`          | Client-side video processing                    |

---

## 6. Bug / Improvement Hotspots

### 🔴 Critical Concerns

| Issue                           | Location                            | Details                                              |
|---------------------------------|-------------------------------------|------------------------------------------------------|
| **No server-side auth middleware** | `academy-server/app.js`          | All API endpoints are unprotected — no JWT/session verification middleware. Anyone with the URL can call any endpoint. |
| **SQL Injection risk**          | `services/*.js`                     | Some queries use string concatenation instead of parameterized queries. Audit thoroughly. |
| **Sensitive data in .env committed** | `academy-server/.env`          | Contains DB credentials (password is empty but structure suggests production exposure risk). |
| **No input validation middleware** | `handlers/*.js`                  | Most handlers trust `req.body` without validation/sanitization. |
| **Single MySQL connection**     | `dbConfig.js`                       | Uses a single `mysql.createConnection()` instead of a connection pool — will fail under concurrent load. |

### 🟡 Code Quality Hotspots

| File                            | Size       | Issue                                                |
|---------------------------------|------------|------------------------------------------------------|
| `readService.js`                | 2050 lines | God class with 77 methods — should be split by domain (courses, users, podcasts, etc.) |
| `Reports.js`                    | 78 KB      | Massive page component — likely mixing UI, data fetching, and business logic |
| `CourseEditPage.js`             | 78 KB      | Very large edit form — should be decomposed into sub-components |
| `CourseOverview.js`             | 48 KB      | Complex overview page — candidate for splitting |
| `UserProfile.js`                | 39 KB      | Large profile page with edit capabilities |
| `PodcastPage.js`                | 39 KB      | Complex podcast detail page |
| `Settings.js`                   | 34 KB      | Admin settings doing too many things |
| `WebinarLibrary.js`             | 34 KB      | Large webinar management page |
| `createHandlers.js`             | 792 lines  | Mixes route handling with file upload logic |
| `createServices.js`             | 29 KB      | Large service file — should be decomposed |

### 🟡 Architecture Issues

| Issue                                     | Details                                                      |
|-------------------------------------------|--------------------------------------------------------------|
| **Three date libraries**                  | `date-fns`, `dayjs`, and `moment` all installed — pick one   |
| **Commented-out code**                    | Large blocks of commented code in handlers (e.g., webinar delete, old registration flow) |
| **Inconsistent HTTP methods**             | Read operations using POST (`/login`, `/fetchBookmark`, `/fetchPrompts`) instead of GET |
| **Duplicate route methods**               | `updateEpisode` exists as both PUT and POST (same handler)   |
| **Duplicate function definitions**        | `fetchBookmark` and `fetchteam` are defined twice in `readService.js` |
| **Minified source files in repo**         | `.min.js` files alongside source files in `handlers/` and `services/` |
| **Mixed MUI + Ant Design**                | Using both `@mui/material` and `antd` — increases bundle size |
| **No test coverage**                      | Test script is placeholder `echo "Error: no test specified"` |
| **No TODO/FIXME comments found**          | Code lacks inline documentation about known issues           |

---

## 7. Quick Start / Running Instructions

### Prerequisites
- **Node.js** (v16+ recommended)
- **MySQL** server running locally
- **npm** or **yarn**

### Database Setup
```sql
-- 1. Create the database
CREATE DATABASE academy;

-- 2. Import the schema/seed files (in order)
SOURCE academy-server/database/login_bonus_tables.sql;
SOURCE academy-server/database/phishing_assessment_tables.sql;
SOURCE academy-server/database/phishing_config.sql;
SOURCE academy-server/database/phishing_questions_seed.sql;

-- Note: The main application tables (course_tbl, user_tbl, module_tbl, etc.)
-- are NOT included as SQL files. You may need to create them manually
-- based on the queries found in services/*.js files.
```

### Backend Setup
```bash
# Navigate to server directory
cd academy-server

# Install dependencies
npm install

# Configure environment (edit .env as needed)
# PORT=8000
# USER=root
# PASSWORD=<your_mysql_password>
# DATABASE=academy
# HOST=localhost

# Run in development mode (auto-restart)
npm run devStart

# Or run in production mode
npm start
```
The server will start on `http://localhost:8000`.

### Frontend Setup
```bash
# Navigate to frontend directory
cd pro-academy-2.0

# Install dependencies
npm install
# or
yarn install

# Start development server
npm start
```
The app will open at `http://localhost:3000`.

### API Configuration
- The frontend's API base URL is set in `pro-academy-2.0/src/config.js`
- Default: `http://localhost:8000`
- For production, uncomment and update the production URL

---

## 8. Additional Notes

### For Junior Developers

1. **Start here**: Read `routes.js` to understand all the pages. Then open a page (e.g., `CoursePage.js`) and trace how it fetches data from the backend.

2. **Data flow pattern**: Almost every page follows the same pattern:
   ```
   useEffect → axios.get/post(baseUrl + "/endpoint") → setState → render
   ```

3. **File uploads go to**: `academy-server/public/materials/` organized by type (podcast_images, episode_audio, episode_thumbnail, webinar, etc.).

4. **User roles** are numeric:
   - `0` = Regular User
   - `1` = Admin
   - `5` = Super Admin

5. **Theme system**: The app supports light/dark mode plus seasonal themes (Christmas, Halloween). Check `utils/christmasAnimation.js` and `utils/halloweenAnimation.js`.

6. **The `sections/@dashboard/` folder** contains the actual UI components rendered inside pages. Pages are often thin wrappers around section components.

7. **The `middleware/` folder** (frontend) contains feature-specific data/logic modules — **not** Express middleware. Don't confuse it with server middleware.

8. **When adding new features**: Follow the existing pattern:
   - Add SQL query in the appropriate `services/*.js` file
   - Add Express route in the matching `handlers/*.js` file
   - Create/update the page in `pages/`
   - Add the route in `routes.js`

9. **No automated tests exist** — manual testing is required. Consider adding tests when modifying critical paths (login, quiz scoring, course progress).

10. **Large files are the biggest risk areas** — `Reports.js`, `CourseEditPage.js`, `readService.js`. Proceed with caution when modifying these.
