<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Donor authentication
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../auth/login.php");
    exit();
}

$donor_id = $_SESSION['donor_id'];
$message = "";

// Fetch donor blood group
$stmt = $conn->prepare("SELECT blood_group FROM donors WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
$blood_group = $donor['blood_group'] ?? "";

// Check last donation (from donations table)
$last_donation = null;
$stmt = $conn->prepare("SELECT donated_at FROM donations WHERE donor_id = ? ORDER BY donated_at DESC LIMIT 1");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $last_donation = $result->fetch_assoc()['donated_at'];
}

// Determine eligibility
$can_donate = true;
$next_eligible = null;

if ($last_donation) {
    $last_date = new DateTime($last_donation);
    $now = new DateTime();
    $diff = $now->diff($last_date)->days;
    if ($diff < 90) {
        $can_donate = false;
        $next_eligible = clone $last_date;
        $next_eligible->modify("+3 months");
    }
}

// Handle donation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_donate) {
    $bag_number = "BAG-" . strtoupper(uniqid());

    // Insert into donations table
    $stmt = $conn->prepare("INSERT INTO donations (donor_id, bag_number, blood_group) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("iss", $donor_id, $bag_number, $blood_group);

    if ($stmt->execute()) {
        // Update storage
        $update = $conn->prepare("UPDATE storage SET bags_available = bags_available + 1 WHERE blood_group = ?");
        if (!$update) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $update->bind_param("s", $blood_group);
        $update->execute();

        $_SESSION['donation_success'] = "Donation recorded successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donate Blood - Blood Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5" style="max-width:600px;">
    <h2 class="text-center text-danger"><i class="fas fa-hand-holding-medical"></i> Donate Blood</h2>

    <?php if ($message) echo $message; ?>

    <?php if ($can_donate): ?>
        <form method="POST" class="border p-4 rounded bg-light shadow text-center">
            <p>Your Blood Group: <strong class="text-danger"><?= htmlspecialchars($blood_group); ?></strong></p>
            <button type="submit" class="btn btn-danger w-50"><i class="fas fa-hand-holding-medical"></i> Donate Now</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            You last donated on <?= $last_date->format('Y-m-d'); ?>.<br>
            You can donate again after 3 months on <?= $next_eligible->format('Y-m-d'); ?>.
        </div>
    <?php endif; ?>

    <div class="mt-3 text-center">
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
