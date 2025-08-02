

# Bank Transfer Invoice Tracking System

This is a lightweight PHP-based system for managing and tracking bank transfer invoices. It is designed with simplicity and clarity in mind, using a modular MVC structure and SQLite3 for ease of deployment. The system supports user authentication, administrative control, invoice uploads, and email notifications.

---

## Features

- Secure user registration and login system
- Role-based access (User and Admin)
- Admin dashboard for managing all invoices
- User dashboard for tracking personal invoices
- Invoice creation with image upload
- Status tracking and email notifications
- Built with PHP 7.1+ and SQLite3
- No external dependencies or frameworks required

---

## Installation

### Requirements

- PHP 7.1 or higher
- SQLite3 enabled in `php.ini`
- A web server (Apache or Nginx recommended)

### Steps

1. Clone the repository:
   ```bash
   git clone git@github.com:almhdy24/bank_invoice_system.git
   cd bank_invoice_system

2. Ensure write permissions for the following folders:

uploads/

db/ (will be created during installation)



3. Run the installer:

Navigate to /install in your browser

This will guide you through setting up the database and creating the initial admin account





---

Security Notes

Sessions are securely managed with token regeneration and expiration

Uploaded files are validated and stored with unique names

Role-based access control restricts admin-only actions

Middleware protects authenticated and admin routes



---

Development Notes

The application follows a custom lightweight MVC pattern

SQLite is used for portability and simplicity

Autoloading uses a simple PSR-4-inspired loader (autoload.php)

Views are written in raw PHP for clarity, but can be swapped with a templating engine



---

Future Improvements

Implement CSRF protection tokens in forms

Expand email features using PHPMailer

Add support for API endpoints (JSON responses)

Export reports and statistics

Add pagination and search features for large datasets



---

License

This project is open-sourced under the MIT License.


