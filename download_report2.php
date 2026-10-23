<?php
session_start();
require 'db.php';
require('fpdf/fpdf.php'); // Make sure fpdf.php is present in fpdf folder

if (!isset($_SESSION['provider_id'])) {
    die("Unauthorized access!");
}
$provider_id = $_SESSION['provider_id'];

if (isset($_POST['date'])) {
    $date = $_POST['date'];
    $stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id=? AND request_date=? ORDER BY request_date DESC");
    $stmt->bind_param("ss", $provider_id, $date);
    $title = "Date Report ($date)";
} elseif (isset($_POST['month'])) {
    $month = $_POST['month'];
    $stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id=? AND DATE_FORMAT(request_date,'%Y-%m')=? ORDER BY request_date DESC");
    $stmt->bind_param("ss", $provider_id, $month);
    $title = "Month Report ($month)";
} elseif (isset($_POST['year'])) {
    $year = $_POST['year'];
    $stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id=? AND YEAR(request_date)=? ORDER BY request_date DESC");
    $stmt->bind_param("ss", $provider_id, $year);
    $title = "Year Report ($year)";
} else {
    die("Invalid request!");
}

$stmt->execute();
$res = $stmt->get_result();

// FPDF Setup
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, $title, 0, 1, 'C');
$pdf->Ln(5);

// Table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(10, 10, "ID", 1, 0, 'C', true);
$pdf->Cell(30, 10, "Sicker", 1, 0, 'C', true);
$pdf->Cell(30, 10, "Service", 1, 0, 'C', true);
$pdf->Cell(20, 10, "Slot", 1, 0, 'C', true);
$pdf->Cell(25, 10, "Date", 1, 0, 'C', true);
$pdf->Cell(55, 10, "Note", 1, 0, 'C', true);
$pdf->Cell(20, 10, "Status", 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);

// Loop through results
while ($r = $res->fetch_assoc()) {
    $pdf->Cell(10, 10, $r['request_id'], 1);
    $pdf->Cell(30, 10, $r['user_name'], 1);
    $pdf->Cell(30, 10, $r['request_type'], 1);
    $pdf->Cell(20, 10, $r['slot'], 1);
    $pdf->Cell(25, 10, date('d-m-Y', strtotime($r['request_date'])), 1);

    // Note (MultiCell) + position fix
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $note = !empty($r['note']) ? $r['note'] : '-';
    $pdf->MultiCell(55, 10, $note, 1);
    $pdf->SetXY($x + 55, $y);

    $pdf->Cell(20, 10, $r['status'], 1, 1);
}

$pdf->Output(str_replace(" ", "_", $title) . ".pdf", "D");
exit;
?>
