# AL NASRAT FOODS (النصرت فوډز) 🏭

Factory & Wholesale Management Dashboard for Inventory, Daily Khata, Wasoli (Collections), and Bakaya (Customer Balances) in Kandahar. Specially designed for local offline shop/factory operation using **Laragon (PHP & MySQL)**.

---

## 🌟 Features (ځانګړتیاوې)

- 📦 **Inventory Management (د خامو موادو لیست):** Real-time stock tracking, minimum alert thresholds, quick stock in/out adjustments, and movement history.
- ▤ **Daily Khata (د حساب کتاب):** Daily factory ledger for debits, credits, and operational expenses.
- ◌ **Wasoli / Collections (وصولي):** Track customer payment collections and clearance statuses.
- ◍ **Customer Balances (بقایا جات):** Customer ledger with business types, locations, outstanding amounts, and payment statuses.
- 👑 **Admin Authentication:** Secure login & session management for the factory owner/admin.
- 💱 **Multi-Currency Support:** AFN (افغانۍ), PKR (روپۍ), and USD ($) conversion support.
- ⚡ **Offline First:** Fully functional on local PC (Laragon / XAMPP) without internet requirement.

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, Tailwind CSS, Vanilla JavaScript (ES6+)
- **Backend:** PHP 8.x (Native PDO)
- **Database:** MySQL 8.x / MariaDB
- **Environment:** Laragon / Localhost

---

## 🚀 Setup & Installation (په لاراګون کې کارول)

1. **Clone or Copy Project:**
   Copy the project folder to your Laragon web directory:
   ```bash
   C:\laragon\www\alnasratfood
   ```

2. **Start Laragon:**
   - Open Laragon and click **"Start All"** (ensuring Apache and MySQL are running).

3. **Database Configuration:**
   - The database (`alnasratfood`) and all required tables are automatically created on first launch via `db.php`.
   - Alternatively, you can import `database.sql` into **phpMyAdmin** (`http://localhost/phpmyadmin`).

4. **Access the System:**
   Open your browser and visit:
   ```text
   http://localhost/alnasratfood/
   ```
   or
   ```text
   http://alnasratfood.test/
   ```

---

## 🔐 Default Admin Credentials

| Parameter | Value |
| :--- | :--- |
| **Username** | `admin` |
| **Password** | `admin123` |
| **Role** | Shop Owner / Factory Admin |

---

## 📁 Project Structure

```text
alnasratfood/
├── index.php          # Main dashboard application
├── login.php          # Admin authentication screen
├── logout.php         # Logout session handler
├── db.php             # Database connection & auto-table creation
├── api.php            # RESTful API for inventory, customers, khata
├── database.sql       # Database schema and initial seed data
├── app.js             # Client-side scripts and API requests
├── styles.css         # Custom styling
└── README.md          # Project documentation
```

---

## 👤 Author
- **GitHub:** [@afghanniamt44-afk](https://github.com/afghanniamt44-afk)
