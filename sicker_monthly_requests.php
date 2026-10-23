<?php
session_start();
require 'db.php';
require('fpdf/fpdf.php');

if (!isset($_SESSION['sicker_id']) || !isset($_POST['month'])) {
    die("Unauthorized or missing month.");
}

$sicker_id = $_SESSION['sicker_id'];
$month = $_POST['month']; // format: YYYY-MM

$stmt = $conn->prepare("SELECT * FROM requests WHERE sicker_id=? AND DATE_FORMAT(request_date,'%Y-%m')=? ORDER BY request_date DESC");
$stmt->bind_param("ss", $sicker_id, $month);
$stmt->execute();
$res = $stmt->get_result();

// Start PDF
$pdf = new FPDF('L','mm','A4'); // Landscape orientation
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,"Sicker Monthly Report - ".date('F Y', strtotime($month.'-01')),0,1,'C');

$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(220,220,220);

// Table header
$pdf->Cell(10,10,"ID",1,0,'C',true);
$pdf->Cell(50,10,"Provider",1,0,'C',true);
$pdf->Cell(50,10,"Service",1,0,'C',true);
$pdf->Cell(30,10,"Slot",1,0,'C',true);
$pdf->Cell(30,10,"Date",1,0,'C',true);
$pdf->Cell(30,10,"Status",1,1,'C',true);

// Table rows
$pdf->SetFont('Arial','',10);
while($r = $res->fetch_assoc()){
    $pdf->Cell(10,10,$r['request_id'],1);
    $pdf->Cell(50,10,$r['provider_id'],1);
    $pdf->Cell(50,10,$r['request_type'],1);
    $pdf->Cell(30,10,$r['slot'],1);
    $pdf->Cell(30,10,date('d-m-Y', strtotime($r['request_date'])),1);
    $pdf->Cell(30,10,$r['status'],1,1);
}

// Output PDF
$pdf->Output("D","Sicker_Monthly_Report_".date('F_Y', strtotime($month.'-01')).".pdf");
exit;
?>
