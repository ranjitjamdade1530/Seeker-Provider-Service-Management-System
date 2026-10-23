<?php
session_start();
include 'db.php';
require('fpdf/fpdf.php');

$sicker_id = $_SESSION['sicker_id'] ?? 1;
$year = $_GET['year'] ?? date('Y');

$stmt = $conn->prepare("SELECT * FROM requests WHERE sicker_id=? AND YEAR(request_date)=? ORDER BY request_date DESC");
$stmt->bind_param("ii", $sicker_id, $year);

$stmt->execute();
$res = $stmt->get_result();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,"Yearly Request Report (Sicker) - $year",0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(15,10,'ID',1);
$pdf->Cell(40,10,'Provider',1);
$pdf->Cell(40,10,'Service',1);
$pdf->Cell(25,10,'Slot',1);
$pdf->Cell(35,10,'Date',1);
$pdf->Cell(30,10,'Status',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
if($res->num_rows > 0){
    while($r = $res->fetch_assoc()){
        $pdf->Cell(15,10,$r['request_id'],1);
        $pdf->Cell(40,10,htmlspecialchars($r['provider_id']),1);
        $pdf->Cell(40,10,htmlspecialchars($r['request_type']),1);
        $pdf->Cell(25,10,htmlspecialchars($r['slot']),1);
        $pdf->Cell(35,10,date('d-m-Y', strtotime($r['request_date'])),1);
        $pdf->Cell(30,10,htmlspecialchars($r['status']),1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(185,10,'No records found for '.$year,1,1,'C');
}

$pdf->Output('D', "Sicker_Yearly_Report_$year.pdf");
exit;
?>
