<?php
require('fpdf.php');
include 'db.php';

// Check if date is submitted
if (isset($_POST['report_date'])) {
    $report_date = $_POST['report_date'];

    $pdf = new FPDF();
    $pdf->AddPage();

    // Header
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,"Service Request Report for $report_date",0,1,'C');

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

    $stmt = $conn->prepare("SELECT * FROM requests WHERE DATE(request_date) = ?");
    $stmt->bind_param("s", $report_date);
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
        $pdf->Cell(0,10,"No requests found for this date.",1,1,'C');
    }

    $pdf->Output('D', "Requests_$report_date.pdf");
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
  <div class="card shadow border-0 mx-auto" style="max-width: 500px;">
    <div class="card-header bg-primary text-white text-center">
      <h5 class="mb-0"> Download Requests Report by Date</h5>
    </div>
    <div class="card-body">
      <form action="download_requests.php" method="POST">
        <div class="mb-3">
          <label for="report_date" class="form-label">Select Date</label>
          <input type="date" name="report_date" id="report_date" class="form-control" required>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-download"></i> Download PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
