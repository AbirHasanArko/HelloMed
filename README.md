<div align="center">
  <img src="hellomed-laravel/public/logo.svg" alt="HelloMed Logo" width="120" />
  <h1>HelloMed</h1>
  <p><b>A Comprehensive Hospital Management & Digital Health Platform</b></p>

  [![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
  [![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)](LICENSE)
</div>

<br/>

## 🏥 About HelloMed

HelloMed is a modern, full-stack hospital management system and digital health platform designed to bridge the gap between patients and healthcare providers. It provides a seamless experience for patients to book appointments (both online and offline), order medicines, request emergency ambulances, and read health articles, all while giving hospital staff, doctors, and administrators powerful tools to manage daily operations.

Developed with a clean, premium teal-and-white aesthetic, HelloMed focuses on user experience, performance, and accessibility.

---

## ✨ Key Features

- **Multi-Role Authentication & Authorization (RBAC)**
  - Five distinct user roles: `Admin`, `Staff`, `Doctor`, `Pharmacist`, and `Patient`.
- **Advanced Appointment System**
  - **Online Consultations:** Book online slots with automated meeting link generation.
  - **Offline/Walk-in:** Dedicated staff panel to instantly register walk-in patients and book physical appointments.
- **Emergency Ambulance Dispatch**
  - Public facing emergency request form with real-time staff dispatch tracking, location sharing, and status updates.
- **Digital E-Pharmacy**
  - Integrated medicine catalog with cart system, digital prescriptions verification, and seamless checkout.
- **Health Knowledge Base & Blog**
  - A fully featured CMS for health articles written by doctors, complete with a commenting system.
- **Dynamic Homepage & Featured Entities**
  - Admin controls to flag specific doctors, departments, and articles as "Featured" to dynamically rearrange the public homepage.
- **Review & Feedback System**
  - Patients can rate and leave feedback on doctor profiles after consultations.
- **Patient-Doctor Chat**
  - Patient-doctor appointment chat with read status and secure file attachment support.
- **Comprehensive Admin Dashboard**
  - Statistical overviews, CMS management, doctor scheduling, and system-wide audit logging.

---

## 🛠 Tech Stack

HelloMed is built on a robust, modern technology stack ensuring scalability and ease of maintenance.

### Backend
- **Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** MySQL (Relational, robust, standard for production environments)
- **Security:** Laravel Sanctum (for any future API needs), built-in CSRF & XSS protection.

### Frontend
- **Templating:** Laravel Blade (Server-side rendering for optimal SEO and performance)
- **Styling:** Custom Vanilla CSS with CSS Variables (No external CSS frameworks required, completely bespoke premium design system)
- **Build Tool:** Vite (for rapid HMR and asset bundling)
- **Interactivity:** Vanilla JavaScript (Lightweight DOM manipulation, dynamic filter submission)

---

## 🏗 Architecture

The platform follows a classic **Monolithic MVC (Model-View-Controller)** architecture, enhanced with Laravel's powerful ecosystem.

### Core Architectural Pillars
1. **Routing & Middleware:** 
   - Strict route grouping based on authentication and role checks (`role:admin,staff`, `role:doctor`, etc.).
2. **Controllers & Form Requests:**
   - Controllers are kept "skinny" by offloading validation to dedicated Form Request classes.
3. **Database Relationships:**
   - Deeply relational MySQL schema utilizing Eloquent ORM. 
   - Notable relations: `User hasOne DoctorProfile`, `Appointment belongsTo Patient & Doctor`, `Article belongsTo Category & Author`.
4. **State Management & UI:**
   - Blade components and global layouts handle the UI state.
   - Dynamic UI elements (like the Featured Doctors filter) use auto-submitting forms for SSR filtering.
5. **Idempotent Migrations:**
   - Schema updates (like adding `is_featured` columns) use `Schema::hasColumn` checks to ensure migrations can be re-run safely in MySQL environments.

---

## 🔄 User Workflows

### 👤 1. Patient Workflow
1. **Onboarding:** Registers an account or browses the public directory as a guest. Ambulance calling does not require login.
2. **Booking:** Filters doctors by department, selects an online or offline slot, and confirms the booking.
3. **Consultation:** Accesses the online meeting link at the scheduled time or visits the hospital.
4. **Post-Consultation:** Receives a digital prescription (PDF downloadable), medicine buying links are automatically added to the prescription. Buys prescribed medicines directly from the platform, and leaves a doctor review.

### 👨‍⚕️ 2. Doctor Workflow
1. **Management:** Logs into the Doctor Dashboard.
2. **Schedule:** Updates availability (days, times, slot durations).
3. **Appointments:** Views upcoming appointments, provides meeting links for online consults, and writes digital prescriptions.
4. **Outreach:** Authors and publishes health articles to the public blog.

### 👔 3. Admin Workflow
1. **Oversight:** Monitors total system statistics (patients, revenue, appointments).
2. **Curation:** Manages Departments, Doctors, and Articles. Toggles the `is_featured` flags to curate the public homepage.
3. **Staffing:** Registers new staff members, doctors, and pharmacists.

### 🚑 4. Staff Workflow
1. **Walk-ins:** Registers new patients on the spot and books offline appointments directly via the internal staff panel.
2. **Ambulance:** Monitors incoming emergency ambulance requests and dispatches vehicles, updating the status in real-time.

### 💊 5. Pharmacist Workflow
1. **Inventory:** Manages medicine stock, prices, and categorization.
2. **Fulfillment:** Reviews patient orders, checks attached digital prescriptions for restricted medicines, and updates order fulfillment statuses.

---

## 🚀 Getting Started

Follow these steps to run HelloMed locally on your machine.

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/hellomed.git
   cd hellomed
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Configuration**
   HelloMed uses MySQL. Create a database named `hellomed` in your MySQL server (e.g., via XAMPP phpMyAdmin) and ensure your `.env` has the correct `DB_CONNECTION=mysql` settings.
   ```bash
   php artisan migrate --seed
   ```
   *(Note: The seeder populates the database with departments, professional male doctors, articles, and medicines).*

6. **Storage Link**
   Link the public storage directory to serve images properly:
   ```bash
   php artisan storage:link
   ```

7. **Run the Application**
   Open two terminals to run the backend and frontend simultaneously:
   
   *Terminal 1:*
   ```bash
   php artisan serve
   ```
   
   *Terminal 2:*
   ```bash
   npm run dev
   ```

8. **Visit `http://localhost:8000`** in your browser!

## Local Setup

Run all commands from inside `hellomed-laravel/`.

1. Install dependencies

```bash
composer install
```

2. Create environment file

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

3. Generate key

```bash
php artisan key:generate
```

4. Prepare database and seed data

```bash
php artisan migrate:fresh --seed
```

5. Create storage symlink (for uploads)

```bash
php artisan storage:link
```

6. Start app

```bash
php artisan serve
```

Open: `http://127.0.0.1:8000`

## One-Command Bootstrap

`composer.json` includes a setup script:

```bash
composer run setup
```

This installs dependencies, prepares `.env`, generates app key, migrates DB, and builds frontend assets.

## Development Commands

Run full dev stack with server, queue listener, log tailing, and Vite:

```bash
composer run dev
```

Run tests:

```bash
php artisan test
```

or:

```bash
composer run test
```

## Operational Jobs

### Scheduler

Appointment reminders and scheduled tasks should run via scheduler:

```bash
php artisan schedule:run
```

Production cron:

```cron
* * * * * cd /path/to/hellomed-laravel && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker

For background notifications and retry flows, run a queue worker/listener:

```bash
php artisan queue:listen --tries=1 --timeout=0
```

### Reminder Command

Manual reminder run:

```bash
php artisan appointments:send-reminders
```

## Seeded Accounts (Common Local Defaults)

- Admin: `admin@hellomed.test` / `password123`
- Staff: `staff@hellomed.test` / `password123`
- Pharmacist: `pharmacist@hellomed.test` / `password123`
- Patient: `patient@hellomed.test` / `password123`
- Doctor: `doctor@hellomed.test` / `password123`

Register patient accounts from the public register page.

## Demo Flow Suggestions

1. Register as patient and book an appointment
2. Confirm appointment from admin/staff panel
3. Open appointment from doctor panel, add meeting link and prescription
4. Login as patient, open appointment details, download PDF, buy medicines
5. Login as pharmacist, review order and open prescription attachment
6. Review audit logs from admin panel

## Deployment Notes

### Recommended (free, closest to production)

- Oracle Cloud Always Free VPS + free subdomain (DuckDNS/No-IP)

### Works for demo only

- InfinityFree + free subdomain

### Not recommended for this full backend

- Vercel-only deployment (serverless mismatch for persistent Laravel workflows)
- Cloudflare-only hosting (good as DNS/CDN/proxy, not a full Laravel host)

## Security and Integrity Notes

- Role middleware protects admin/staff/doctor/pharmacist/patient areas
- Appointment and order updates apply status/payment constraints
- Inventory commit/release safeguards exist for payment lifecycle
- Audit logs capture sensitive operational changes
- Auth includes failed login and lockout-related event handling

## Known Constraints

- Payment providers are mock/test flows in current implementation
- Chat uses polling, not WebSocket realtime
- Some advanced workflows require scheduler/queue processes to be running
- Free shared hosting may not support reliable queues/scheduler behavior

## Troubleshooting

### Prescriptions or uploads not opening

- Run `php artisan storage:link`
- Confirm `storage` and `bootstrap/cache` are writable
- Use route-based secure file endpoints where configured (for pharmacist order prescriptions)

### Route/config changes not reflected

```bash
php artisan optimize:clear
```

### Database issues after schema updates

```bash
php artisan migrate:fresh --seed
```

---
---

<div align="center">
  <p><b>Developed with ❤️ for the future of digital healthcare.</b></p>
  <p><i>Developed by Abir Hasan Arko</i></p>
</div>

---
---