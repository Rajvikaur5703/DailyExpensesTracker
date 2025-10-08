<!DOCTYPE html>
<html>
<head>
    <title>Admin Profiles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;              /* side by side layout */
            justify-content: space-between; /* push left & right */
            padding: 20px;
            background: #f2f2f2;
        }
        .form-container {
            width: 40%;  /* each form takes 40% space */
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            text-align: center;
        }
        .form-container input {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover {
            background: #0a58ca;
        }
    </style>
</head>
<body>

    <!-- Your Profile -->
    <div class="form-container">
        <h2>My Profile</h2>
        <form action="profile.php" method="POST">
            <input type="text" name="fullname" placeholder="Full Name" value="Syed Sadiya">
            <input type="email" name="email" placeholder="Email">
            <input type="date" name="dob">
            <input type="text" name="gender" placeholder="Gender">
            <input type="text" name="mobile" placeholder="Mobile">
            <button type="submit" class="btn">Save</button>
        </form>
    </div>

    <!-- Friend's Profile -->
    <div class="form-container">
        <h2>Friend's Profile</h2>
        <form action="profile.php" method="POST">
            <input type="text" name="fullname" placeholder="Full Name" value="Your Friend’s Name">
            <input type="email" name="email" placeholder="Email">
            <input type="date" name="dob">
            <input type="text" name="gender" placeholder="Gender">
            <input type="text" name="mobile" placeholder="Mobile">
            <button type="submit" class="btn">Save</button>
        </form>
    </div>

</body>
</html>