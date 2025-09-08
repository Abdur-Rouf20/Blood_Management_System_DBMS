
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/index.php">
      <i class="fas fa-tint"></i> BMS
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['role'])): ?>
          <?php if($_SESSION['role']==='donor'): ?>
            <li class="nav-item"><a class="nav-link" href="/blood_management_system/donor/dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/blood_management_system/donor/donate.php"><i class="fas fa-hand-holding-medical"></i> Donate</a></li>
          <?php elseif($_SESSION['role']==='patient'): ?>
            <li class="nav-item"><a class="nav-link" href="/blood_management_system/patient/dashboard.php"><i class="fas fa-user"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/blood_management_system/patient/request.php"><i class="fas fa-file-medical"></i> Request Blood</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="/blood_management_system/includes/search_donor.php"><i class="fas fa-search"></i> Search Donor</a></li>
          <li class="nav-item"><a class="nav-link" href="/blood_management_system/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

