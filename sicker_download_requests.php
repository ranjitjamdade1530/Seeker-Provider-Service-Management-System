<?php
session_start();
require 'db.php';
require('fpdf/fpdf.php');

if (!isset($_SESSION['sicker_id']) || !isset($_POST['report_date'])) {
    die("Unauthorized or missing report date.");
}

$sicker_id = $_SESSION['sicker_id'];
$report_date = $_POST['report_date'];

$stmt = $conn->prepare("SELECT * FROM requests WHERE sicker_id=? AND request_date=? ORDER BY request_date DESC");
$stmt->bind_param("ss", $sicker_id, $report_date);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("No records found for " . date('d-m-Y', strtotime($report_date)));
}

// Start PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, "Sicker Report - " . date('d-m-Y', strtotime($report_date)), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(10, 10, "ID", 1, 0, 'C', true);
$pdf->Cell(40, 10, "Provider", 1, 0, 'C', true);
$pdf->Cell(40, 10, "Service", 1, 0, 'C', true);
$pdf->Cell(30, 10, "Slot", 1, 0, 'C', true);
$pdf->Cell(30, 10, "Status", 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
while ($r = $res->fetch_assoc()) {
    $pdf->Cell(10, 10, $r['request_id'], 1);
    $pdf->Cell(40, 10, $r['provider_id'], 1);
    $pdf->Cell(40, 10, $r['request_type'], 1);
    $pdf->Cell(30, 10, $r['slot'], 1);
    $pdf->Cell(30, 10, $r['status'], 1, 1);
}

$pdf->Output("D", "Sicker_Report_" . date('d-m-Y', strtotime($report_date)) . ".pdf");
exit;
