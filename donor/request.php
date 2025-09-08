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

// Handle blood request form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_group = $_POST['blood_group'];
    $bags_requested = (int)$_POST['bags_requested'];

    // Check storage
    $stmt_check = $conn->prepare("SELECT bags_available FROM storage WHERE blood_group = ?");
    $stmt_check->bind_param("s", $blood_group);
    $stmt_check->execute();
    $result = $stmt_check->get_result()->fetch_assoc();
    $available = $result['bags_available'] ?? 0;

    // Decide status
    if ($available >= $bags_requested) {
        $status = 'success';
        // Reduce storage
        $update = $conn->prepare("UPDATE storage SET bags_available = bags_available - ? WHERE blood_group = ?");
        $update->bind_param("is", $bags_requested, $blood_group);
        $update->execute();
        $message = "<div class='alert alert-success'>Request successful! Generate crossmatch report below.</div>";
    } else {
        $status = 'pending';
        $message = "<div class='alert alert-warning'>Not enough blood available. Only $available bag(s) available. Request is pending.</div>";
    }

    // Insert request
    $stmt_insert = $conn->prepare("INSERT INTO requests (donor_id, blood_group, bags_requested, status) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("isis", $donor_id, $blood_group, $bags_requested, $status);
    $stmt_insert->execute();
}

// Fetch all donor requests with donor name
$stmt_requests = $conn->prepare("
    SELECT r.*, d.name AS donor_name
    FROM requests r
    JOIN donors d ON r.donor_id = d.id
    WHERE r.donor_id = ?
    ORDER BY r.created_at DESC
");
$stmt_requests->bind_param("i", $donor_id);
$stmt_requests->execute();
$requests = $stmt_requests->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Blood Requests - Blood Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center text-danger"><i class="fas fa-file-medical"></i> Donor Blood Requests</h2>

    <?= $message ?>

    <!-- Request Form -->
    <div class="card p-4 mb-5 shadow">
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label>Blood Group</label>
                <select name="blood_group" class="form-select" required>
                    <option value="">-- Select Blood Group --</option>
                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group) echo "<option value='$group'>$group</option>"; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Number of Bags</label>
                <input type="number" name="bags_requested" class="form-control" min="1" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-danger w-100"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-danger">
                <tr>
                    <th>ID</th>
                    <th>Donor Name</th>
                    <th>Blood Group</th>
                    <th>Bags Requested</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th>Crossmatch Report</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $requests->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['donor_name'] ?></td>
                    <td><?= $row['blood_group'] ?></td>
                    <td><?= $row['bags_requested'] ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <?php if ($row['status'] === 'success'): ?>
                            <a href="generate_crossmatch.php?request_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-file-pdf"></i> Generate PDF</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>


