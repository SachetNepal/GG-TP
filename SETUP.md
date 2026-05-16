# Run GroceryGo (GG-TP) on another device — same database

This repo is set up so **both machines use the same Oracle database** (`192.168.1.64`, user `NEPSA`) and the same app URLs. After clone you only install PHP dependencies once.

> **Security:** `.env`, `db.php`, and PayPal keys are committed for team convenience. Use a **private** Git repository only.

## Requirements (each PC)

| Requirement | Notes |
|-------------|--------|
| **XAMPP** (or Apache + PHP) | PHP **8.2+** |
| **PHP OCI8** | Required for Oracle (`php_oci8` in `php.ini`) |
| **Composer** | [getcomposer.org](https://getcomposer.org) |
| **Network** | PC must reach Oracle at **`192.168.1.64:1521`** (same LAN/VPN as DB server) |
| **Folder path** | Clone to **`C:\xampp\htdocs\GG-TP`** so URLs match (`/GG-TP`, `/GG-TP/trader-portal`) |

## One-time setup on a new PC

```powershell
cd C:\xampp\htdocs
git clone <your-repo-url> GG-TP
cd GG-TP

# Install PHP packages (vendor/ is not in git)
composer install

# .env is in the repo; if missing, copy from example:
# copy .env.example .env

php artisan config:clear
php artisan storage:link
```

Optional (email on new XAMPP): copy Gmail/sendmail settings into `C:\xampp\sendmail\sendmail.ini` from your first machine.

## URLs

| App | URL |
|-----|-----|
| Customer (Laravel) | http://localhost/GG-TP/ |
| Trader portal | http://localhost/GG-TP/trader-portal/login.php |
| DB test | http://localhost/GG-TP/test-db.php |

## What is committed (no manual DB/password edits)

- **`.env`** — Laravel DB, PayPal sandbox, mail, `APP_KEY`, `APP_URL`
- **`db.php`** — Oracle user/password/TNS for trader portal + `test-db.php`
- **`trader-portal/config.php`** — `APP_BASE`, `PORTAL_BASE`
- **All application code**, `composer.lock`, assets, SQL notes under `database/sql/`

## What is NOT in git (generated on each PC)

| Item | Command |
|------|---------|
| `vendor/` | `composer install` |
| `node_modules/` | Only if you use Vite: `npm install && npm run build` |
| Logs / cache | Created at runtime under `storage/` |

## Same database on every device

Data lives on the **shared Oracle server**, not in the repo. Every PC uses the same:

- Host: `192.168.1.64:1521/XEPDB1`
- Schema user: `NEPSA`
- Same product/order/user IDs (`P1`, `U1`, etc.)

If the second PC cannot connect, check firewall/VPN and that Oracle listener allows remote clients.

## Optional overrides (usually not needed)

- **`db.local.php`** — gitignored; overrides `db.php` only on one machine
- **`trader-portal/config.php`** — change `PORTAL_BASE` only if you do not use `htdocs/GG-TP`

## Email verification schema (once per database)

If signup OTP columns are missing:

```powershell
php artisan grocery:email-verification-schema
```

Or run `database/sql/oracle-email-verification.sql` in SQL Developer.

## Quick verify

```powershell
php test-db.php
php artisan route:list --name=home
```

Browser: open http://localhost/GG-TP/ and log in with an existing customer account from Oracle.
