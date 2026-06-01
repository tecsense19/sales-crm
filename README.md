# Premium Sales CRM Dashboard

A modern, production-ready Sales CRM and Email Campaign platform built on **Laravel 12**, **Tailwind CSS**, and **Alpine.js**. This application is designed to streamline client management, sales pipelines, data importing, and personalized email marketing in a unified, beautifully styled dashboard.

---

## 🚀 Key Modules & Features

### 📊 1. Core Analytics Dashboard
* **Key Metrics**: Real-time summary cards displaying Total Clients, Active Campaigns, Pipeline Conversion Rates, and Monthly Revenue.
* **Interactive Visualization**: Sleek visual charts powered by ApexCharts with custom date filters to track conversion stats over time.
* **Pipeline Monitoring**: Interactive tables showing active deals, campaign progression, and client onboarding activity.

### 📋 2. Interactive Client Kanban Board
* **Visual Sales Funnel**: Manage leads across 10 distinct funnel stages: *New, Interested, Contacted, In Progress, Follow Up, On Hold, Converted, Closed Won, Closed Lost, and Not Interested*.
* **Drag-and-Drop Interactivity**: Smooth, reactive drag-and-drop sorting powered by SortableJS/AlpineJS.
* **Stage Transitions**: Visual highlights and instant micro-animations indicating status updates when cards are moved.

### 📥 3. Smart Data Import (CSV / Excel / XLS)
* **File Drag-and-Drop Area**: Modern upload drop-zone with file type validations (`.csv`, `.xlsx`, `.xls`) and custom file size limits.
* **Dynamic Column Mapping**: Intelligent header matching to map import columns automatically to CRM fields (Name, Email, Phone, Location).
* **Validation & Management**: Error reporting for incorrect schema headers and interactive options to remove/replace files before execution.

### ✉️ 4. Campaign Creator & Email Marketing
* **Template Library**: Pre-built email templates with modular custom tags for quick draft generation.
* **Load Templates Instantly**: Small, compact dropdown in the content designer area to auto-populate Subject Line and HTML content instantly.
* **Dynamic Personalization**: Send custom variables (e.g. `{{name}}`, `{{email}}`, `{{location}}`) to both CRM-stored clients and externally uploaded spreadsheet lists.
* **Background Processing**: Mass campaign emails are processed asynchronously using Laravel Queues.

---

## 🛠️ Technology Stack
* **Backend**: Laravel 12, PHP 8.2+
* **Frontend UI**: Tailwind CSS, Alpine.js (Lightweight reactivity)
* **Database**: SQLite (Default) or MySQL
* **Libraries**: 
  * `Maatwebsite/Laravel-Excel` for spreadsheet imports and campaign lists.
  * `SortableJS` for Kanban drag-and-drop interface.
  * `ApexCharts` for premium charts.

---

## ⚙️ Quick Start & Installation

### Step 1: Install Dependencies
Run composer and npm to pull in packages:
```bash
composer install
npm install
```

### Step 2: Configure Environment
Copy `.env.example` to create your environment configuration:
```bash
cp .env.example .env
```
Generate the app key:
```bash
php artisan key:generate
```

### Step 3: Run Database Migrations
Create your database and run the schema setup:
```bash
php artisan migrate
```
If you would like to start with seed data:
```bash
php artisan db:seed
```

### Step 4: Storage Setup
Create the symbolic link to expose campaign or import uploads publicly:
```bash
php artisan storage:link
```

---

## 🏃 Running the Application

### Development Mode (Recommended)
Start the concurrent development environment (server, asset builder, and queue worker) in a single command:
```bash
composer run dev
```

Or run them individually in separate terminal sessions:

* **Laravel Web Server**:
  ```bash
  php artisan serve
  ```
* **Vite Hot Reload Builder**:
  ```bash
  npm run dev
  ```
* **Background Mail/Queue Worker**:
  ```bash
  php artisan queue:work
  ```

---

## 🧪 Testing and Quality
Run the Pest test suite:
```bash
php artisan test
```

For clearing all view, configuration, or database caches:
```bash
php artisan optimize:clear
```
