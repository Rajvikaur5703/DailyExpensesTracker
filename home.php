<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker - Home</title>
    <link rel="stylesheet" href="style/home.css">
</head>
<body>
    <!-- Navbar -->
     <header class="navbar">
        <div class="logo">MoneyMap</div>
        <nav>
            <a href="#">Home</a>
            <a href="#features">Features</a>
            <!-- <a href="about.php">About Us</a> -->
            <a href="login.php" class="nav-btn">Login</a>
        </nav>
    </header>

    <!-- Hero -->
     <section class="hero">
        <div class="hero-text">
            <h1>Manage Your <span>Expenses</span> Smarter</h1>
            <p>Take control of your expenses and Effortless management for smarter business decisions</p>
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary">Get Started</a>
                <a href="login.php" class="btn btn-outline">Login</a>
            </div>
        </div>
        <div class="hero-img">
            <img src="assets/images/expense-tracker" alt="Expense Dashbaord">
        </div>
     </section>

     <!-- Features -->
      <section class="features" id="features">
        <h2>Why Choose Expense Tracker?</h2>
        <div class="feature-cards">
            <div class="feature-card">
                <img src="assets/images/money-bag.png" alt="Track expenses">
                <h3>Track Expenses</h3>
                <p>Easily add and categorize your daily expenses in just a few clicks.</p>
            </div>
            <div class="feature-card">
                <img src="assets/images/combo-chart.png" alt="Reports">
                <h3>Visual Reports</h3>
                <p>Understand your spending with a clean dashbaord and charts.</p>
            </div>
            <div class="feature-card">
                <img src="assets/images/budget.png" alt="Secure">
                <h3>Secure Data</h3>
                <p>Your expenses are safe with secure login authentication.</p>
            </div>
        </div>
      </section>

      <!-- Footer -->
       <footer>
        <div class="footer-contact">
            <h4>Email</h4>
            <p>kaurrajvi34@gmail.com & syedsadiya1711@gmail.com</p>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Daily Expenses Tracker || Developed by <b>Rajvi Kaur</b> & <b>Syed Sadiya</b></p>
        </div>
       </footer>
</body>
</html>

