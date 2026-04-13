# Jones Auto — Car Sales Management App

A PHP + MySQL web application for managing vehicles, customers, employees, sales, payments, repairs, warranties, and purchases at Jones Auto.

---

## Quick Start (Docker — recommended)

The easiest way to run the app. Docker handles PHP, Apache, and MySQL automatically — no XAMPP required.

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (includes Docker Compose)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/<your-username>/barecarsales_app.git
cd barecarsales_app

# 2. Create your local environment file from the template
cp .env.example .env

# 3. (Optional) Open .env and change DB_PASSWORD to something stronger

# 4. Build and start everything
docker compose up --build
```

Once the containers are running, open your browser to:

```
http://localhost/barecarsales_app/
```

The database schema is loaded automatically on first start. No manual SQL import needed.

### Stopping the app

```bash
docker compose down          # stops containers, keeps database data
docker compose down -v       # stops containers AND deletes database data (fresh start)
```

### Rebuilding after code changes

The `volumes` mount in `docker-compose.yml` means PHP file changes are reflected immediately — no rebuild needed. If you change the `Dockerfile` itself (e.g. add a PHP extension), rebuild with:

```bash
docker compose up --build
```

---

## Manual Setup (XAMPP)

If you prefer to run the app locally without Docker:

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Clone or copy this folder into `C:\xampp\htdocs\barecarsales_app\` (Windows) or `/Applications/XAMPP/htdocs/barecarsales_app/` (Mac).
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) and run `properscript.sql` to create the database and tables.
4. Open `db.php` and set the credentials to match your local MySQL setup (default XAMPP: user `root`, no password).
5. Visit `http://localhost/barecarsales_app/`.

---

## Environment Variables

| Variable | Default | Description |
|---|---|---|
| `DB_HOST` | `localhost` | MySQL host (`db` inside Docker) |
| `DB_USER` | `root` | MySQL username |
| `DB_PASSWORD` | *(empty)* | MySQL password — set in `.env` |
| `DB_NAME` | `carsales_app` | Database name |

These are read by `db.php` at runtime. For XAMPP, you can set them in your system environment or edit `db.php` directly.

---

## Project Structure

```
barecarsales_app/
├── Dockerfile              # PHP 8.2 + Apache image
├── docker-compose.yml      # Web + MySQL services
├── .env.example            # Credential template (copy to .env)
├── .gitignore
├── db.php                  # Database connection (reads env vars)
├── header.php              # Shared nav/header
├── footer.php              # Shared footer
├── style.css               # Global stylesheet
├── index.php               # Dashboard / home page
├── properscript.sql        # Full database schema
├── customer/               # Customer CRUD
├── employee/               # Employee CRUD
├── vehicle/                # Vehicle CRUD + filters
├── sale/                   # Sale CRUD
├── payment/                # Payment CRUD
├── repair/                 # Repair CRUD
├── warranty/               # Warranty CRUD
├── purchase/               # Purchase CRUD
├── employment_history/     # Customer employment history CRUD
├── reports/                # Analytics / reports page
└── docs/                   # Project report and tutorial
```

---

## Tech Stack

- **Backend:** PHP 8.2 (procedural, MySQLi with prepared statements)
- **Database:** MySQL 8
- **Frontend:** Vanilla HTML/CSS
- **Server:** Apache (via XAMPP or Docker)
