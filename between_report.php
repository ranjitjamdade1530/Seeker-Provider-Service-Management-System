<?php
require('fpdf.php');
include 'db.php';

// Check if both dates are set
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $from = $_POST['start_date'];
    $to = $_POST['end_date'];

    $pdf = new FPDF();
    $pdf->AddPage();

    // Header
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,"Service Request Report: $from to $to",0,1,'C');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(10,10,"ID",1);
    $pdf->Cell(30,10,"User Name",1);
    $pdf->Cell(30,10,"Mobile",1);
    $pdf->Cell(20,10,"Provider",1);
    $pdf->Cell(30,10,"Type",1);
    $pdf->Cell(25,10,"City",1);
    $pdf->Cell(25,10,"Area",1);
    $pdf->Cell(20,10,"Status",1);
    $pdf->Ln();

    // Fetch data
    $stmt = $conn->prepare("SELECT * FROM requests WHERE DATE(request_date) BETWEEN ? AND ?");
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    $result = $stmt->get_result();

    $pdf->SetFont('Arial','',9);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(10,10,$row['request_id'],1);
            $pdf->Cell(30,10,$row['user_name'],1);
            $pdf->Cell(30,10,$row['user_mobile'],1);
            $pdf->Cell(20,10,$row['provider_id'],1);
            $pdf->Cell(30,10,$row['request_type'],1);
            $pdf->Cell(25,10,$row['city'],1);
            $pdf->Cell(25,10,$row['area'],1);
            $pdf->Cell(20,10,$row['status'],1);
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(0,10,"No requests found in selected range.",1,1,'C');
    }

    $pdf->Output('D', "Requests_$from-to-$to.pdf");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container mt-4">
  <div class="card shadow border-0 mx-auto" style="max-width: 600px;">
    <div class="card-header bg-primary text-white text-center">
      <h5 class="mb-0">Download Requests Report</h5>
    </div>
    <div class="card-body">
      <form method="POST" action="generate_requests_pdf.php" target="_blank">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
          </div>
        </div>
        <div class="text-center mt-3">
          <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-download"></i> Download PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
