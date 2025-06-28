<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<h1 align="center">Kontak</h1>

<p align="center">
  A simple Laravel-based contact management system with authentication.
</p>

---

## 🚀 Getting Started

These instructions will get your Laravel development environment set up for **Kontak**.

### 🛠️ Prerequisites

Make sure you have the following installed:

-   PHP >= 8.2
-   Composer
-   Node.js & npm
-   SQLite (or any database you prefer)

---

### 📦 Clone the repository

```bash
git clone https://github.com/your-username/kontak.git
cd kontak
```

### 🔧 Set up the project

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 🧱 Use MySQL

In `.env` file, modify your password

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kontak
DB_USERNAME=root
DB_PASSWORD=<your_password_here>
```

Then, run the migrations

```bash
php artisan migrate
```

### 🧑‍💻 Start the dev server

Install frontend dependencies and build assets:

```bash
npm install
```

Start Laravel server and frontend client:

```bash
composer run dev
```

Visit http://127.0.0.1:8000 in your browser.

---

## 🎭 Demo Data & Test Accounts

To populate the database with sample data for testing and demonstration:

```bash
php artisan db:seed --class=DemoDataSeeder
```

### 👤 Login Credentials

All demo accounts use the password: **`password`**

#### 🏢 Main Account (Contact Book Owner)
- **Email:** `developers@kontak.app`
- **Name:** Kontak Developers
- **Role:** Owner
- **Features:** Full access to personal contact book with 65+ sample contacts

#### 👨‍💼 Admin Users (Full CRUD Access)
- **Mark Flores:** `mark.flores@kontak.app`
- **Isaeus Guiang:** `isaeus.guiang@kontak.app`
- **Jedric Vicente:** `jedric.vicente@kontak.app`
- **Ceferino Arrey:** `ceferino.arrey@kontak.app`
- **Rainier Reyes:** `rainier.reyes@kontak.app`

*Admin users have full CRUD access to Kontak Developers' contact book but empty personal contact books.*

#### 👥 Audience Users (Read-Only Access)
- **Marketing:** `marketing@kontak.app`
- **Sales:** `sales@kontak.app`
- **Customer Service:** `support@kontak.app`

*Audience users have read-only access to Kontak Developers' contact book with copy-to-clipboard functionality.*

### 📋 Sample Data Includes:
- **65+ realistic contacts** covering all alphabet letters (A-Z)
- **Multiple contact methods** (phones, emails, websites)
- **Professional information** (job titles, addresses, notes)
- **Role-based access control** demonstration
- **Complete contact management** workflow examples

---

### 🔄 Pushing to GitHub

Commit and push

```bash
git add .
git commit -m "Summary of what you've done"
git push -u origin main
```
