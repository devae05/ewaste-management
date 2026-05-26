# ♻️ E-Waste Management System

A PHP + MySQL web application that allows users to schedule e-waste pickup requests, and admins to accept or reject them.

---

## Features

| Role  | Capabilities |
|-------|-------------|
| **User** | Register / Login, Schedule pickups, Track booking status, View accepted history |
| **Admin** | View all users, Accept / Reject pickup requests, View completed history |

---

## Tech Stack

- **Backend** — PHP 8.x (procedural, MySQLi with prepared statements)
- **Database** — MySQL 8.x
- **Frontend** — HTML5, CSS3, Tailwind CSS (CDN), Font Awesome (CDN)
- **Server** — Apache / XAMPP / WAMP (any PHP host)

---

## Project Structure

```
ewaste-management/
├── index.html            # Public landing page
├── login_register.php    # User login & registration
├── logout.php            # Session destroy + redirect
├── auth_check.php        # Reusable auth guard (require_login / require_admin)
├── db.php                # Database connection (MySQLi)
├── schema.sql            # Database schema + seed admin account
│
├── user.php              # User dashboard (home)
├── Schedule.php          # Schedule a new pickup
├── booking.php           # User's personal booking status
├── history.php           # Accepted bookings (user sees own; admin sees all)
│
├── admin.php             # Admin dashboard
├── manage_users.php      # Admin: view all users
└── manage_pickups.php    # Admin: accept / reject pending pickups
```

---

## Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/ewaste-management.git
```

### 2. Move to your web server root
```
# XAMPP (Windows)
C:\xampp\htdocs\ewaste-management\

# XAMPP (macOS)
/Applications/XAMPP/htdocs/ewaste-management/

# WAMP
C:\wamp64\www\ewaste-management\
```

### 3. Create the database
Open **phpMyAdmin** (or your MySQL client) and run:
```sql
SOURCE /path/to/ewaste-management/schema.sql;
```

### 4. Configure database credentials
Edit **`db.php`** and set your MySQL credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // ← your MySQL password
define('DB_NAME', 'ewaste');
```

### 5. Set the admin password
The `schema.sql` seeds a default admin account with username **`admin`** and password **`password`**.

> ⚠️ **Change this immediately** after first login by updating the DB directly, or add a change-password feature.

To generate a new bcrypt hash:
```php
<?php echo password_hash('YourNewPassword', PASSWORD_DEFAULT); ?>
```

### 6. Start XAMPP / WAMP and visit
```
http://localhost/ewaste-management/
```

---

## Default Credentials

| Role  | Username | Password  |
|-------|----------|-----------|
| Admin | `admin`  | `password` |

> Admin accounts are **not** self-registerable. Only the seeded admin exists by default.

---

## Security Notes

- All database queries use **prepared statements** (no raw SQL injection risk).
- Passwords are hashed with **`password_hash()`** (bcrypt).
- Sessions use **`session_regenerate_id(true)`** on login to prevent session fixation.
- Every protected page includes `auth_check.php` to enforce login/admin guards.
- `htmlspecialchars()` is used on all user-supplied output to prevent XSS.

---

## Screenshots

> Add screenshots of your pages here (drag images into the GitHub editor).

---

## License

MIT — free to use, modify, and distribute.
