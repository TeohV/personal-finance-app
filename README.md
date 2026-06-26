# FinTrack Personal Finance App

FinTrack is a Laravel-based personal finance web application for tracking accounts, income, expenses, monthly budgets, savings goals, and basic admin user management.

The project is designed for an academic group assignment, so the workflow is intentionally simple and easy to understand while still using a more realistic accounting model than only "income" and "expense".

## Professional Project Rating

**Rating: 7.5/10 for an academic project**

From a professional perspective, this project is above average for a student assignment because it includes authentication, authorization, CRUD workflows, validation, tests, a consistent frontend design, and a clearer accounting model using accounts and transfers.

Strengths:

- Clear Laravel MVC structure.
- Good user ownership checks for financial records.
- Income, expense, account, transfer, budget, and goal workflows are separated logically.
- Transfers correctly handle cases like withdrawing money from bank to cash.
- Admin routes are protected.
- Automated feature tests cover important workflows.
- UI is simple, consistent, and suitable for an academic system.

Current limitations:

- Not yet production-grade accounting because it does not use a full double-entry ledger.
- No audit log for financial changes.
- No recurring income or recurring expense support.
- No charts, exports, or advanced reports.
- No CI workflow configured for GitHub Actions yet.
- No role management screen for creating the first admin automatically.

For a real commercial finance product, the rating would be closer to **6/10**, mainly because production systems need stronger auditability, reporting, backups, and accounting controls.

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
- Consistent Blade-based frontend design.

## Accounting Logic

The app uses a simple accounting workflow:

| Action | Effect |
| --- | --- |
| Add income | Increases the selected account balance |
| Add expense | Decreases the selected account balance |
| Transfer money | Moves money from one account to another |
| Withdraw from bank | Recorded as a transfer from Bank to Cash |

Example:

If a user withdraws RM 200 from a bank account:

- It is **not income** because the user did not earn new money.
- It is **not an expense** because the user did not spend money.
- It is a **transfer** from `Bank` to `Cash`.

This keeps income reports, expense reports, and monthly budgets accurate.

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

## Demo User

If you run the seeder, it creates:

```text
Email: test@example.com
Password: password
```

To make a user an admin for local testing, run:

```bash
php artisan tinker
```

Then:

```php
\App\Models\User::where('email', 'test@example.com')->update(['role' => 'admin']);
```

## Testing

Run all tests:

```bash
composer test
```

Run code style check:

```bash
composer exec pint -- --test
```

Automatically fix formatting:

```bash
composer exec pint
```

Build frontend assets:

```bash
npm run build
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

## Before Pushing to GitHub

Run this checklist before pushing:

1. Make sure `.env` is not committed.
2. Make sure no secrets, passwords, or real database credentials are in `.env.example`.
3. Make sure `vendor`, `node_modules`, `.pnpm-store`, and `public/build` are not committed.
4. Run the test suite:

```bash
composer test
```

5. Run the formatter check:

```bash
composer exec pint -- --test
```

6. Build frontend assets to confirm Vite works:

```bash
npm run build
```

7. Clear local Laravel caches if needed:

```bash
php artisan optimize:clear
```

8. Check pending files:

```bash
git status --short
```

If Git says `not a git repository`, initialize the project first:

```bash
git init
git branch -M main
git remote add origin <your-github-repository-url>
```

9. Commit with a clear message:

```bash
git add .
git commit -m "Improve finance workflow with accounts and transfers"
```

10. Push to GitHub:

```bash
git push origin main
```

If your branch is named `master`, use:

```bash
git push origin master
```

## Suggested Future Improvements

- Add reports by month, category, and account.
- Add charts for income, expenses, and asset growth.
- Add recurring income and recurring expenses.
- Add export to CSV or PDF.
- Add audit logs for financial record changes.
- Add GitHub Actions for automated tests.
- Add a setup page or command for creating the first admin user.
- Add soft deletes for important financial records.

## License

This project is for academic use. If reused publicly, add the appropriate license for your group or institution.
