# MGIS 445 Team Project

This repository is scaffolded for the Spring 2026 MGIS 445 team project: a screen-printed T-shirt storefront with account management, inventory, cart, checkout, and team/company pages.

## Recommended stack

- Frontend and server rendering: PHP
- Database: MySQL
- Styling: plain CSS
- Mail for receipt flow: PHP mailer or mocked email logging during development

This matches the assignment's shared hosting plus MySQL deployment model and keeps the project easy to demo on `people.rit.edu`.

## Project structure

- `public/`: web root and PHP pages
- `public/includes/`: shared layout, config, and helper functions
- `public/assets/`: CSS and JavaScript
- `db/schema.sql`: relational schema
- `db/seed.sql`: starter catalog and demo data
- `docs/`: planning artifacts, sitemap, ERD notes, and checklist

## Run locally (Windows + XAMPP)

After cloning:

1. Open PowerShell in the project root.
2. Start PHP's built-in server using XAMPP's PHP executable:

```powershell
cd "C:\Users\madig\MGIS Project"
C:\xampp\php\php.exe -S localhost:8000 -t public
```

3. Open `http://localhost:8000/` in your browser.

Optional: add `C:\xampp\php` to your PATH so you can run `php -S localhost:8000 -t public` without the full executable path.

## Milestones

1. Deliverable 2: finalize schema, ERD, and database setup.
2. Deliverable 3: finish layout shell, navigation, and sitemap.
3. Deliverable 4: implement catalog and item views.
4. Deliverable 5: implement cart, totals, tax, shipping, and inventory enforcement.
5. Deliverable 6: implement About Us and Contact Us workflow.
6. Deliverable 7: finish report, testing pass, and demo polish.

## Immediate next build steps

1. Replace placeholder company text with your team brand.
2. Add the 9 real shirt designs, colors, pricing, and descriptions to `db/seed.sql`.
3. Wire `public/includes/config.php` to your MySQL credentials.
4. Build registration, login, logout, checkout, and receipt email flows.
