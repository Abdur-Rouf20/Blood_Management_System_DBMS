<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Only logged-in users can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['donor', 'patient'])) {
    header("Location: ../auth/login.php");
    exit();
}

$search_blood_group = "";
$search_address = "";
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['blood_group'])) {
        $search_blood_group = $_GET['blood_group'];
    }
    if (!empty($_GET['address'])) {
        $search_address = trim($_GET['address']);
    }

    $query = "SELECT name, blood_group, phone, address FROM donors WHERE 1=1";
    $params = [];
    $types = "";

    if ($search_blood_group) {
        $query .= " AND blood_group = ?";
        $types .= "s";
        $params[] = $search_blood_group;
    }

    if ($search_address) {
        $query .= " AND address LIKE ?";
        $types .= "s";
        $params[] = "%" . $search_address . "%";
    }

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Donors - Blood Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center text-danger"><i class="fas fa-search"></i> Search Donors</h2>

    <form method="GET" class="row g-3 justify-content-center mt-4 mb-4">
        <div class="col-md-3">
            <input type="text" name="address" class="form-control" placeholder="Search by address" value="<?= htmlspecialchars($search_address); ?>">
        </div>
        <div class="col-md-3">
            <select name="blood_group" class="form-select">
                <option value="">-- Select Blood Group --</option>
                <?php
                $groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                foreach ($groups as $group) {
                    $selected = ($search_blood_group === $group) ? "selected" : "";
                    echo "<option value='$group' $selected>$group</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-danger w-100"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>

    <?php if ($results && $results->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-danger">
                    <tr>
                        <th>Name</th>
                        <th>Blood Group</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $results->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= $row['blood_group']; ?></td>
                            <td><?= htmlspecialchars($row['phone']); ?></td>
                            <td><?= htmlspecialchars($row['address']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
        <p class="text-center text-muted mt-3">No donors found matching your criteria.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
