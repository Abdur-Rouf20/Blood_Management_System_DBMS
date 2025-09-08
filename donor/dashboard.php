<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Protect route: only donors can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch donor info
$donor_id = $_SESSION['donor_id'];
$stmt = $conn->prepare("SELECT name, email, blood_group, phone, address FROM donors WHERE id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Donor Dashboard - Blood Management System</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5">
  <div class="text-center mb-4">
    <h2 class="text-danger"><i class="fas fa-tint"></i> Donor Dashboard</h2>
    <p class="lead">Welcome, <strong><?= htmlspecialchars($donor['name']); ?></strong> (<?= $donor['blood_group']; ?>)</p>
  </div>

  <div class="row g-4 text-center">
    <div class="col-md-3">
      <a href="donate.php" class="btn btn-danger w-100 p-3 shadow">
        <i class="fas fa-hand-holding-medical fa-2x"></i><br>Donate Blood
      </a>
    </div>
    <div class="col-md-3">
      <a href="request.php" class="btn btn-warning w-100 p-3 shadow">
        <i class="fas fa-file-medical fa-2x"></i><br>My Requests
      </a>
    </div>
    <div class="col-md-3">
      <a href="/blood_management_system/includes/search_donor.php" class="btn btn-success w-100 p-3 shadow">
        <i class="fas fa-search fa-2x"></i><br>Search Donors
      </a>
    </div>
    <div class="col-md-3">
      <a href="profile.php" class="btn btn-info w-100 p-3 shadow text-white">
        <i class="fas fa-user-edit fa-2x"></i><br>Update Profile
      </a>
    </div>
  </div>

  <div class="mt-4 text-center">
    <a href="../auth/logout.php" class="btn btn-dark w-25 p-2 shadow">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
