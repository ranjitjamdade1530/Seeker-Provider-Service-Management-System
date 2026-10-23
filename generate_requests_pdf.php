<?php
require('fpdf.php');
include 'db.php';

// Validate POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Validate date format (optional but recommended)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        die("Invalid date format.");
    }

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Title
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Requests Report: $startDate to $endDate", 0, 1, 'C');
    $pdf->Ln(5);

    // Header row
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'ID', 1);
    $pdf->Cell(30, 10, 'User Name', 1);
    $pdf->Cell(30, 10, 'Mobile', 1);
    $pdf->Cell(20, 10, 'Provider', 1);
    $pdf->Cell(30, 10, 'Type', 1);
    $pdf->Cell(25, 10, 'City', 1);
    $pdf->Cell(25, 10, 'Area', 1);
    $pdf->Cell(20, 10, 'Status', 1);
    $pdf->Ln();

    // Fetch data
    $stmt = $conn->prepare("SELECT * FROM requests WHERE DATE(request_date) BETWEEN ? AND ? ORDER BY request_id DESC");
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $pdf->SetFont('Arial', '', 9);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(10, 10, $row['request_id'], 1);
            $pdf->Cell(30, 10, $row['user_name'], 1);
            $pdf->Cell(30, 10, $row['user_mobile'], 1);
            $pdf->Cell(20, 10, $row['provider_id'], 1);
            $pdf->Cell(30, 10, $row['request_type'], 1);
            $pdf->Cell(25, 10, $row['city'], 1);
            $pdf->Cell(25, 10, $row['area'], 1);
            $pdf->Cell(20, 10, $row['status'], 1);
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(0, 10, "No requests found in this date range.", 1, 1, 'C');
    }

    $pdf->Output('D', "Request_Report_{$startDate}_to_{$endDate}.pdf");
} else {
    echo "Invalid request.";
}
?>
