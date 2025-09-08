<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Protect route
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$message = "";

// Fetch current patient info
$stmt = $conn->prepare("SELECT name, email, blood_group, phone, address FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $blood_group = $_POST['blood_group'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']); // optional

    // Check if email is used by another patient
    $check = $conn->prepare("SELECT id FROM patients WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $patient_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Email already in use by another patient!</div>";
    } else {
        if ($password !== "") {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE patients SET name=?, email=?, blood_group=?, phone=?, address=?, password=? WHERE id=?");
            $stmt->bind_param("ssssssi", $name, $email, $blood_group, $phone, $address, $password_hash, $patient_id);
        } else {
            $stmt = $conn->prepare("UPDATE patients SET name=?, email=?, blood_group=?, phone=?, address=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $email, $blood_group, $phone, $address, $patient_id);
        }

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
            // Refresh patient info
            $stmt = $conn->prepare("SELECT name, email, blood_group, phone, address FROM patients WHERE id = ?");
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();
            $patient = $stmt->get_result()->fetch_assoc();
        } else {
            $message = "<div class='alert alert-danger'>Failed to update profile. Try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile - Patient</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5" style="max-width:600px;">
    <h2 class="text-center text-info"><i class="fas fa-user-edit"></i> Update Profile</h2>
    <?= $message; ?>

    <form method="POST" class="border p-4 rounded bg-light shadow">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($patient['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($patient['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Blood Group</label>
            <select name="blood_group" class="form-select" required>
                <?php
                $groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                foreach ($groups as $group) {
                    $selected = ($patient['blood_group'] === $group) ? "selected" : "";
                    echo "<option value='$group' $selected>$group</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($patient['phone']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" required><?= htmlspecialchars($patient['address']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">New Password <small>(Leave blank to keep current)</small></label>
            <input type="password" name="password" class="form-control">
        </div>

        <button type="submit" class="btn btn-info w-100"><i class="fas fa-save"></i> Update Profile</button>
    </form>

    <div class="mt-3 text-center">
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
