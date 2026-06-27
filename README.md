# FinTrack Personal Finance App

FinTrack is a Laravel-based personal finance web application for tracking accounts, income, expenses, monthly budgets, savings goals, and basic admin user management.

## Main Features

- User registration, login, logout, and profile management.
- Account tracking for assets such as cash, bank, e-wallet, savings, investment, and other accounts.
- Income tracking with category and account selection.
- Expense tracking with category, account, date, notes, search, and filters.
- Transfers between accounts.
- Monthly budget allocation by expense category.
- Financial goals with reserved progress tracking.
- Admin user overview, role update, ban or unban, and financial review.
- Security middleware for banned users and admin-only routes.

## Accounting Logic

The app uses a simple accounting workflow:

| Action | Effect |
| --- | --- |
| Add income | Increases the selected account balance |
| Add expense | Decreases the selected account balance |
| Transfer money | Moves money from one account to another |
| Withdraw from bank | Recorded as a transfer from Bank to Cash |

## Tech Stack

- PHP 8.2+
- Laravel 12
- Blade templates
- MySQL or SQLite
- Laravel Breeze authentication
- Vite
- Tailwind dependencies and custom CSS
- PHPUnit for tests
- Laravel Pint for code style

## Requirements

Install these before running the project:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL or MariaDB, such as XAMPP, Laragon, WAMP, or a local MySQL service
- Git

## Installation

Clone the repository:

```bash
git clone <your-repository-url>
cd personal-finance-app
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
copy .env.example .env
```

On macOS or Linux, use:

```bash
cp .env.example .env
```

Generate the Laravel app key:

```bash
php artisan key:generate
```

## Database Setup

### Option 1: MySQL

Start MySQL first. For example, if using XAMPP, open the XAMPP Control Panel and start **MySQL**.

Create a database:

```sql
CREATE DATABASE personal_finance_db;
```

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=personal_finance_db
DB_USERNAME=root
DB_PASSWORD=
```

Then run migrations:

```bash
php artisan migrate
```

Optional seed data:

```bash
php artisan db:seed
```

### Option 2: SQLite

The default `.env.example` uses SQLite. Create the SQLite database file:

```bash
type nul > database/database.sqlite
```

On macOS or Linux, use:

```bash
touch database/database.sqlite
```

Then run:

```bash
php artisan migrate
```

## Running the App

Start the Laravel development server:

```bash
php artisan serve
```

In another terminal, run Vite:

```bash
npm run dev
```

Open the app:

```text
http://127.0.0.1:8000
```

## Important Pages

| Page | Purpose |
| --- | --- |
| Dashboard | Monthly summary, total assets, accounts, recent activity, budgets, and goals |
| Accounts | Manage cash, bank, e-wallet, savings, and transfers |
| Income | Record received money into an account |
| Expenses | Record spending from an account |
| Categories | Manage income and expense categories |
| Allocations | Plan monthly budgets |
| Financial Goals | Track savings targets and reserved progress |
| Admin | Manage users and review user financial summaries |


