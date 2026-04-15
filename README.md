# MGIS 445 Team Project - Inkforge Tees

This repository contains the Spring 2026 MGIS 445 team project: a screen-printed T-shirt storefront with account management, inventory, cart, checkout, and company pages.

## Recommended Stack
- Frontend and server rendering: **PHP**
- Database: **MySQL**
- Styling: plain CSS
- Email: PHP mailer (or mocked during development)

This project is designed for deployment on RIT’s shared hosting (`people.rit.edu`).

## Important: Base Path Configuration

This project runs in a **subfolder** on shared hosting.

**After cloning** the repository, you **must** update the basepath:

1. Open `includes/config.php`
2. Change this line to match your username and project folder:

```php
$basepath = '/mcm5381/TeeForgedTees/';   // ← CHANGE THIS index.php should be in this folder 