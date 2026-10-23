<?php
require 'db.php';
require_once('fpdf/fpdf.php'); // Path to FPDF library

session_start(); // Ensure session is started

if(isset($_POST['date']) && isset($_SESSION['provider_id'])){
    $provider_id = $_SESSION['provider_id'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id=? AND request_date=? ORDER BY request_date DESC");
    $stmt->bind_param("ss", $provider_id, $date);
    $stmt->execute();
    $res = $stmt->get_result();

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'Date Report', 0, 1, 'C');
            $this->Ln(5);
        }

        function TableHeader() {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(200, 220, 255);
            $this->Cell(10, 8, 'ID', 1, 0, 'C', true);
            $this->Cell(30, 8, 'Sicker', 1, 0, 'C', true);
            $this->Cell(30, 8, 'Service', 1, 0, 'C', true);
            $this->Cell(30, 8, 'Slot', 1, 0, 'C', true);
            $this->Cell(60, 8, 'Note', 1, 0, 'C', true);
            $this->Cell(25, 8, 'Status', 1, 1, 'C', true);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Add Date
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Date: ' . $date, 0, 1);

    // Table Header
    $pdf->TableHeader();

    // Table Rows
    $pdf->SetFont('Arial', '', 10);
    while($r = $res->fetch_assoc()) {
        $note = !empty($r['note']) ? $r['note'] : '-';

        $pdf->Cell(10, 8, $r['request_id'], 1);
        $pdf->Cell(30, 8, $r['user_name'], 1);
        $pdf->Cell(30, 8, $r['request_type'], 1);
        $pdf->Cell(30, 8, $r['slot'], 1);
        $pdf->Cell(60, 8, $note, 1);
        $pdf->Cell(25, 8, $r['status'], 1);
        $pdf->Ln();
    }

    // Output PDF
    $filename = "Report_" . $date . ".pdf";
    $pdf->Output('D', $filename); // Force download

    exit;
} else {
    echo "Invalid Request!";
}
?>
