<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link rel="stylesheet" href="../style/admin_profile.css">
</head>
<body>

  <div class="profile-card">
    <h2 class="title">Syed Sadiya</h2>
    <form class="profile-form" method="POST" action="profile.php">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="Syed Sadiya AliAbbas">

         <label>Email</label>
        <input type="email" name="email" value="syedsadiya1711@gmail.com">

        <label>Date of Birth</label>
        <input type="text" name="dob" value="17-11-2005">

        <label>Gender</label>
        <input type="text" name="gender" value="Female">

        <label>Mobile</label>
        <input type="text" name="mobile" value="9662212293">

      </div>
      <!-- Buttons -->
      <div class="form-actions">
        <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
        <button type="submit" class="btn btn-save">Save</button>
      </div>
    </form>
  </div>

  <div class="profile-card">
    <h2 class="title">Rajvi Kaur</h2>
    <form class="profile-form" method="POST" action="profile.php">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="Palrey Rajvi Kaur Ranjit Singh">

         <label>Email</label>
        <input type="email" name="email" value="rajvi@gmail.com">

        <label>Date of Birth</label>
        <input type="text" name="dob" value="10-03-2004">

        <label>Gender</label>
        <input type="text" name="gender" value="Female">

        <label>Mobile</label>
        <input type="text" name="mobile" value="7698497014">

      </div>

      <!-- Buttons -->
      <div class="form-actions">
        <a href="dashboard.php" class="btn btn-cancel">Cancel</a>
        <button type="submit" class="btn btn-save">Save</button>
      </div>
    </form>
  </div>

</body>
</html>
