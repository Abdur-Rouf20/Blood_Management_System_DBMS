<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../includes/db.php');
require('../includes/fpdf/fpdf.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

if (!isset($_GET['request_id'])) {
    die("Invalid request ID.");
}

$request_id = (int)$_GET['request_id'];

// Fetch request info
$stmt = $conn->prepare("SELECT r.*, p.name AS patient_name, d.name AS donor_name
                        FROM requests r
                        LEFT JOIN donors d ON d.blood_group = r.blood_group
                        LEFT JOIN patients p ON p.id = r.patient_id
                        WHERE r.id = ? AND r.patient_id = ?");
$stmt->bind_param("ii", $request_id, $patient_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request || $request['status'] !== 'success') {
    die("Crossmatch report not available for this request.");
}

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Title
$pdf->Cell(0,10,'Crossmatch Report',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);

// Request Info
$pdf->Cell(50,10,'Request ID:',0,0);
$pdf->Cell(0,10,$request['id'],0,1);

$pdf->Cell(50,10,'Patient Name:',0,0);
$pdf->Cell(0,10,$request['patient_name'],0,1);

$pdf->Cell(50,10,'Donor Name:',0,0);
$pdf->Cell(0,10,$request['donor_name'] ?? 'N/A',0,1);

$pdf->Cell(50,10,'Blood Group:',0,0);
$pdf->Cell(0,10,$request['blood_group'],0,1);

$pdf->Cell(50,10,'Bags Requested:',0,0);
$pdf->Cell(0,10,$request['bags_requested'],0,1);

$pdf->Cell(50,10,'Status:',0,0);
$pdf->Cell(0,10,ucfirst($request['status']),0,1);

$pdf->Cell(50,10,'Requested At:',0,0);
$pdf->Cell(0,10,$request['created_at'],0,1);

$pdf->Ln(10);
$pdf->Cell(0,10,'This is an official crossmatch report for the requested blood.',0,1,'C');

// Output PDF for download
$pdf->Output('D','Crossmatch_Report_Request_'.$request['id'].'.pdf');
exit();
