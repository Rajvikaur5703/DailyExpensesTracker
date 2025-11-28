# 🧾 Daily Expenses Tracker

A simple, responsive web application to track daily expenses. Built using **HTML**, **CSS**, **JavaScript**, **PHP**, and **MySQL**.  
This project helps users log, manage, and view their day-to-day spending through a clean and easy interface.

---

## 🚀 Features

- ➕ Add new expenses  
- ✏️ Edit existing expenses  
- ❌ Delete expenses  
- 📅 Add date, description, category, and amount  
- 📊 View total daily spending  
- 💾 MySQL database storage  
- 📱 Responsive design for all devices  
- 🔐 Basic JavaScript validation  

---

## 🛠️ Tech Stack

| Layer | Technologies |
|-------|--------------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Tools Used | XAMPP / phpMyAdmin |

---

## 📂 Project Structure

DailyExpensesTracker/
|-home.php
|-login.php
|-logout.php
|-register.php
|-admin/
|  |-admin_dashboardphp
|  |-admin_profile.php
|  |-category.php
|  |-generate_report.php
|  |-manage_admin.php
|  |-manage_expenses.php
|  |-manage_users.php
|assets/
| |-images/
|  /sql/
|-config/
|-fpdf/
|-includes/
|  |-create admin.php
|  |-session_check.php
|-styles/
|  |-admin/
|  |-user/
|-home.css
|-login.css
|-register.css
|-users/
|  |-add_income.php
|  |-change_password.php
|  |-dashboard.php
|  |-generate_report.php
|  |-profile.php
|  |-reports.php
|  |-view_expense.php


Running the Project on WAMP (Complete Guide)
Follow these steps to set up and run the Daily Expenses Tracker using WAMP.
🟩 1. Install & Start WAMP
Install WAMP Server (64-bit recommended)
Start WAMP → make sure the icon is green (Apache & MySQL running)

🟩 2. Move Project into WAMP Directory
Place the project folder inside:
C:\wamp64\www\DailyExpensesTracker\

3. FPDF Setup (Required Files Only)

Only two FPDF items are needed:

fpdf/
│── fpdf.php
└── font/

4. Create the Database in phpMyAdmin
Open browser and go to:
http://localhost/phpmyadmin/
Click Databases
Create new database:
expense_tracker
Go to Import → upload:
database/expense_tracker.sql

5. Configure Database Connection (WAMP Settings)
Open db_connect.php and use:
<?php
$servername = "localhost";
$username = "root";
$password = "";   // WAMP uses empty password
$dbname = "expense_tracker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

6. Run the Project in Browser
Open:
http://localhost/DailyExpensesTracker/
If folder name is different, adjust the URL.
