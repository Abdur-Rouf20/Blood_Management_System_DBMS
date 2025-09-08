<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../includes/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$message = "";

// Handle request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_group = $_POST['blood_group'];
    $bags_requested = (int)$_POST['bags_requested'];

    // Check storage availability
    $stmt = $conn->prepare("SELECT bags_available FROM storage WHERE blood_group = ?");
    if (!$stmt) die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    $stmt->bind_param("s", $blood_group);
    $stmt->execute();
    $result = $stmt->get_result();
    $available = ($result->num_rows > 0) ? $result->fetch_assoc()['bags_available'] : 0;

    $status = ($available >= $bags_requested) ? 'success' : 'pending';

    // Reduce storage only if available
    if ($status === 'success') {
        $update = $conn->prepare("UPDATE storage SET bags_available = bags_available - ? WHERE blood_group = ?");
        $update->bind_param("is", $bags_requested, $blood_group);
        $update->execute();
    }

    // Insert patient request
    $insert = $conn->prepare("INSERT INTO requests (patient_id, blood_group, bags_requested, status) VALUES (?, ?, ?, ?)");
    $insert->bind_param("isis", $patient_id, $blood_group, $bags_requested, $status);
    if ($insert->execute()) {
        $message = "<div class='alert alert-success'>Request submitted successfully! Status: $status</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $insert->error . "</div>";
    }
}

// Fetch all patient requests
$stmt = $conn->prepare("SELECT * FROM requests WHERE patient_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$requests = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Blood Requests</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center text-danger"><i class="fas fa-file-medical"></i> Blood Requests</h2>

    <?= $message ?>

    <!-- Request Form -->
    <div class="card p-4 mb-5 shadow">
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label>Blood Group</label>
                <select name="blood_group" class="form-select" required>
                    <option value="">-- Select Blood Group --</option>
                    <?php
                    $groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                    foreach ($groups as $group) echo "<option value='$group'>$group</option>";
                    ?>
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
