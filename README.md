# PrintTime — printing business website (Laravel 12)

Public marketing site + customer portal (schools, businesses, individuals) + admin panel for quotations.

## Run locally
```
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan serve
```
Admin login (from `database/seeders/AdminSeeder.php`): `alliybutt3@gmail.com` — change the password after first login.

## Flow
Customer registers → creates a request (items, quantities, options, files) → admin sets unit prices at `/admin/quotes` →
customer is emailed, reviews, accepts/declines or chats → admin moves status through production → completed.
Each customer only ever sees their own records; admin sees everything under Customers.

## Deploy on Render
1. Push this folder to GitHub. In Render: **New → Blueprint**, pick the repo (uses `render.yaml`, `Dockerfile`).
2. Fill the prompted env vars (`APP_URL`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, business details).
3. `APP_KEY`: run `php artisan key:generate --show` locally and paste it into the `APP_KEY` env var.
4. Uploaded customer files live in `storage/app`. Render's disk is ephemeral unless you attach the persistent disk
   (needs a paid plan; configured in `render.yaml`). Without it, files vanish on each deploy.
5. Emails use `MAIL_MAILER=log` by default (nothing is sent). Set SMTP vars to send real emails.
6. Render's free Postgres expires after 30 days — use a paid database for real use.

Change business name/phone/WhatsApp/currency via env vars (`config/business.php`). Products are editable in the admin panel.
