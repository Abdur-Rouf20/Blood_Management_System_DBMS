<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Blood Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include('includes/navbar.php'); ?>


<div class="container text-center mt-5">

  <!-- Logo/Icon -->
  <div class="mb-4">
    <i class="fas fa-hand-holding-medical fa-4x text-danger"></i>
  </div>

  <!-- Heading -->
  <h1 class="text-danger fw-bold">Welcome to Blood Management System</h1>
  <p class="lead text-muted">Donate Blood, Save Lives ❤️</p>


  <!-- Extra Info Section -->
  <div class="mt-5">
    <h3 class="text-danger"><i class="fas fa-tint"></i> Why Donate Blood?</h3>
    <p class="text-muted">
      Every donation can save up to <strong>3 lives</strong>. Be the reason for someone’s heartbeat today.
    </p>
  </div>

</div>
<div class="container" style="text-align:center; margin-top:100px;">

    <div style="margin-top:50px;">
        <a href="donor/login.php" class="btn btn-primary" style="margin: 10px; padding: 15px 30px;">Donor Login</a>
        <a href="patient/login.php" class="btn btn-success" style="margin: 10px; padding: 15px 30px;">Patient Login</a>
    </div>

    <div style="margin-top:30px;">
        <p>New User? Register here:</p>
        <a href="donor/register.php" class="btn btn-outline-primary" style="margin: 10px; padding: 10px 25px;">Donor Registration</a>
        <a href="patient/register.php" class="btn btn-outline-success" style="margin: 10px; padding: 10px 25px;">Patient Registration</a>
    </div>
</div>

<?php include('includes/footer.php'); ?>
</body>
</html>