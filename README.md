# issues-tracker

Quick start to run the app locally.

## Setup

1) Prepare environment file
- Rename `\.env.example` to `.env` (remove the `.example` part).
- Update database credentials in `.env` if needed.

2) Install dependencies

```
composer install
npm install
```

3) Migrate and seed the database

```
php artisan migrate:fresh --seed
```

4) Start the development server

```
php artisan serve
```

## Seeded Users
The database seeder creates these users (password for all is `password`):

- Argjend Kurteshi — arku@pritech.eu
- Test User — test@pritech.com
- Filan Fisteku — filan@pritech.com

## Notes
- If you encounter “No application encryption key has been specified.” run:

```
php artisan key:generate
```
