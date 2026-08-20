# HR Dashboard — Employee Attendance & Leave Management System

An internal HR management dashboard built as a full-stack application, combining a Laravel frontend with a Spring Boot REST API backend and PostgreSQL database. The system handles employee attendance tracking, leave requests (permission, sick leave, WFH), broadcasts, and reimbursements.

---

## Architecture

```
┌─────────────┐        REST API        ┌──────────────┐        JPA/Hibernate       ┌──────────────┐
│   Laravel   │  ────────────────────▶ │  Spring Boot │  ────────────────────────▶ │  PostgreSQL  │
│  (Frontend) │  ◀────────────────────  │    (API)     │  ◀────────────────────────  │   Database   │
└─────────────┘         JSON            └──────────────┘                            └──────────────┘
```

- **Laravel** handles the UI, authentication/session, role-based routing, and calls the Spring Boot API via HTTP for all business data (users, attendance, leave, broadcasts, reimbursements)
- **Spring Boot** exposes REST endpoints, contains the business logic and validation, and talks to PostgreSQL through Spring Data JPA
- **PostgreSQL** stores all persistent application data

This separation means the frontend has no direct database access — everything goes through the API layer.

---

## Tech Stack

**Frontend**

- Laravel (PHP)
- Blade templates
- Tailwind CSS
- Vite

**Backend**

- Spring Boot 3.2.4 (Java 17)
- Spring Data JPA / Hibernate
- Spring Security
- Lombok
- Gradle

**Database**

- PostgreSQL

---

## Project Structure

```
├── frontend/                  # Laravel application
│   ├── app/                   # (Models, Providers)
│   ├── Http/
│   │   ├── Controllers/       # AuthController, UserController
│   │   └── Middleware/        # CheckRole (role-based access)
│   ├── Services/              # HTTP clients to Spring Boot API
│   │   ├── UserAPIService.php
│   │   ├── UserDataAPIService.php
│   │   ├── UserAttendanceAPIService.php
│   │   ├── WfhDataAPIService.php
│   │   ├── PermissionDataAPIService.php
│   │   ├── BroadcastAPIService.php
│   │   └── BroadcastResponseAPIService.php
│   ├── Models/                # Local Eloquent models (auth-related)
│   ├── resources/views/       # Blade templates (dashboard, forms, reports)
│   ├── routes/web.php         # Route definitions
│   ├── database/migrations/   # Auth-related tables (users, sessions)
│   ├── .env.example
│   └── .gitignore
│
└── backend/                    # Spring Boot API
    ├── src/main/java/com/example/workproject1/
    │   ├── controller/         # REST controllers
    │   ├── model/              # JPA entities
    │   ├── repository/         # Spring Data repositories
    │   ├── dto/                # Data transfer objects
    │   ├── service/            # Business logic (leave calculation, holidays)
    │   └── config/             # SecurityConfig
    ├── src/main/resources/
    │   └── application.properties.example
    ├── build.gradle
    └── .gitignore
```

---

## Features

- **Authentication & Role-Based Access** — Admin and Employee roles with route-level middleware protection
- **Attendance Tracking** — Clock-in/clock-out with photo capture, attendance history and summary reports
- **Leave Management**
    - Permission (leave) requests with approval workflow
    - Sick leave requests with doctor's letter upload support
    - Work-from-home (WFH) requests with approval workflow
- **Reimbursement** — Employee reimbursement submission and tracking
- **Broadcast** — Admin can send announcements to employees; employees can accept/reject
- **Export** — PDF export for user data and attendance summaries
- **Google Calendar Integration** — Helper for syncing leave/attendance events

---

## Setup & Installation

### Prerequisites

- PHP ≥ 8.1, Composer
- Node.js & npm
- Java 17
- PostgreSQL
- Gradle (or use the included `gradlew` wrapper)

### 1. Backend (Spring Boot)

```bash
cd backend
cp src/main/resources/application.properties.example src/main/resources/application.properties
```

Edit `application.properties` with your local PostgreSQL credentials:

```properties
spring.datasource.url=jdbc:postgresql://localhost:5432/your_database_name
spring.datasource.username=your_db_username
spring.datasource.password=your_db_password
```

Run the API:

```bash
./gradlew bootRun
```

The API will start on `http://localhost:8080` by default.

### 2. Frontend (Laravel)

```bash
cd frontend
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set the Spring Boot API URL and any local auth database credentials:

```env
SPRINGBOOT_API_URL=http://localhost:8080/api
```

Run migrations (for auth-related tables only — business data lives in the Spring Boot side):

```bash
php artisan migrate
```

Start the dev server:

```bash
php artisan serve
npm run dev
```

---

## Notes

- Business data (users, attendance, leave, broadcasts, reimbursements) is owned and persisted by the Spring Boot service — Laravel's local database/migrations only cover authentication-related tables
- Attendance clock-in photos are stored in `frontend/storage/app/public/attendance_photos/` and are excluded from version control (see `.gitignore`)
- `application.properties.example` and `.env.example` contain placeholder values only — copy and fill in your own local credentials before running
