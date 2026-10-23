<?php
session_start();
require('fpdf/fpdf.php');
include("db.php");

if (!isset($_SESSION['provider_id'])) {
    die("Unauthorized");
}

$provider_id = $_SESSION['provider_id'];
$report_date = $_POST['report_date'] ?? '';

if (!$report_date) {
    die("Date required.");
}

$query = "SELECT * FROM requests WHERE provider_id = ? AND request_date = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $provider_id, $report_date);
$stmt->execute();
$result = $stmt->get_result();

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont("Arial", "B", 14);
$pdf->Cell(0, 10, "Provider Request Report - $report_date", 0, 1, "C");
$pdf->Ln(5);

$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(20, 10, "ID", 1);
$pdf->Cell(30, 10, "User", 1);
$pdf->Cell(30, 10, "Mobile", 1);
$pdf->Cell(30, 10, "Service", 1);
$pdf->Cell(25, 10, "City", 1);
$pdf->Cell(25, 10, "Slot", 1);
$pdf->Cell(30, 10, "Status", 1);
$pdf->Ln();

$pdf->SetFont("Arial", "", 10);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 10, $row['request_id'], 1);
        $pdf->Cell(30, 10, $row['user_name'], 1);
        $pdf->Cell(30, 10, $row['user_mobile'], 1);
        $pdf->Cell(30, 10, $row['request_type'], 1);
        $pdf->Cell(25, 10, $row['city'], 1);
        $pdf->Cell(25, 10, $row['slot'], 1);
        $pdf->Cell(30, 10, $row['status'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, "No requests found.", 1, 1, "C");
}

$pdf->Output("D", "provider_report_$report_date.pdf");
?>
