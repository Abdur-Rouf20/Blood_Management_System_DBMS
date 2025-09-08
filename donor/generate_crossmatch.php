<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../includes/db.php');
require('../includes/fpdf/fpdf.php'); // Make sure FPDF is in this path

// Donor authentication
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'donor') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['request_id'])) {
    die("Request ID not specified.");
}

$request_id = (int)$_GET['request_id'];

// Fetch request details
$stmt = $conn->prepare("
    SELECT r.id, r.blood_group, r.bags_requested, r.status, r.created_at,
           d.name AS donor_name, d.email AS donor_email, d.phone AS donor_phone
    FROM requests r
    JOIN donors d ON r.donor_id = d.id
    WHERE r.id = ? AND r.status = 'success'
");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No successful request found for this ID.");
}

$data = $result->fetch_assoc();

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Header
$pdf->Cell(0,10,'Blood Crossmatch Report',0,1,'C');
$pdf->Ln(5);

// Donor Info
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,8,'Donor Name:',0,0);
$pdf->Cell(0,8,$data['donor_name'],0,1);

$pdf->Cell(50,8,'Email:',0,0);
$pdf->Cell(0,8,$data['donor_email'],0,1);

$pdf->Cell(50,8,'Phone:',0,0);
$pdf->Cell(0,8,$data['donor_phone'],0,1);

$pdf->Ln(5);

// Request Info
$pdf->Cell(50,8,'Request ID:',0,0);
$pdf->Cell(0,8,$data['id'],0,1);

$pdf->Cell(50,8,'Blood Group:',0,0);
$pdf->Cell(0,8,$data['blood_group'],0,1);

$pdf->Cell(50,8,'Bags Requested:',0,0);
$pdf->Cell(0,8,$data['bags_requested'],0,1);

$pdf->Cell(50,8,'Status:',0,0);
$pdf->Cell(0,8,ucfirst($data['status']),0,1);

$pdf->Cell(50,8,'Requested At:',0,0);
$pdf->Cell(0,8,$data['created_at'],0,1);

$pdf->Ln(10);
$pdf->Cell(0,8,'This is an official crossmatch report.',0,1,'C');

// Output PDF
$pdf->Output('D', 'crossmatch_request_'.$data['id'].'.pdf'); // Force download
exit;
?>
