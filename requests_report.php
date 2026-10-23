<?php
include 'db.php';
require('fpdf/fpdf.php'); // ✅ Ensure FPDF is installed

// Initialize variables
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date   = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Fetch data between date range
$query = "SELECT * FROM requests WHERE 1";
$params = [];
$types = '';

if(!empty($from_date) && !empty($to_date)){
    $query .= " AND DATE(request_date) BETWEEN ? AND ?";
    $params = [$from_date, $to_date];
    $types = "ss";
}
$query .= " ORDER BY request_date DESC";

$stmt = $conn->prepare($query);
if(!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// ✅ PDF Download
if(isset($_GET['download']) && $_GET['download'] == 'pdf'){
    class PDF extends FPDF {
        function Header(){
            $this->SetFont('Arial','B',14);
            $this->Cell(0,10,'Requests Report',0,1,'C');
            $this->Ln(5);
        }
        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,'Generated on '.date('d-m-Y h:i A'),0,0,'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',12);
    if(!empty($from_date) && !empty($to_date))
        $pdf->Cell(0,10,"From: $from_date  To: $to_date",0,1,'L');
    else
        $pdf->Cell(0,10,"All Requests",0,1,'L');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(12,8,'ID',1);
    $pdf->Cell(25,8,'Sicker Name',1);
    $pdf->Cell(25,8,'Provider ID',1);
    $pdf->Cell(25,8,'Type',1);
    $pdf->Cell(20,8,'City',1);
    $pdf->Cell(20,8,'Area',1);
    $pdf->Cell(25,8,'Slot',1);
    $pdf->Cell(25,8,'Date',1);
    $pdf->Cell(18,8,'Status',1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',8);
    while($row = $result->fetch_assoc()){
        // ✅ Format request_date to only date
        $date_only = date('Y-m-d', strtotime($row['request_date']));

        $pdf->Cell(12,8,$row['request_id'],1);
        $pdf->Cell(25,8,$row['user_name'],1);
        $pdf->Cell(25,8,$row['provider_id'],1);
        $pdf->Cell(25,8,$row['request_type'],1);
        $pdf->Cell(20,8,$row['city'],1);
        $pdf->Cell(20,8,$row['area'],1);
        $pdf->Cell(25,8,$row['slot'],1);
        $pdf->Cell(25,8,$date_only,1);
        $pdf->Cell(18,8,$row['status'],1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'Requests_Report_'.date('d-m-Y').'.pdf');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Requests Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .container { margin-top: 40px; }
    .card { border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    th { background-color: #007bff; color: white; }
  </style>
</head>
<body>

<div class="container">
  <div class="card p-4">
    <h3 class="text-center mb-4">📅 Requests Report (By Date Range)</h3>

    <form method="GET" class="row g-3">
      <div class="col-md-3">
        <label class="form-label">From Date</label>
        <input type="date" name="from_date" value="<?php echo $from_date; ?>" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">To Date</label>
        <input type="date" name="to_date" value="<?php echo $to_date; ?>" class="form-control" required>
      </div>
      <div class="col-md-3 align-self-end">
        <button type="submit" class="btn btn-primary w-100">🔍 Show Report</button>
      </div>
      <?php if(!empty($from_date) && !empty($to_date)): ?>
      <div class="col-md-3 align-self-end">
        <a href="?from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>&download=pdf" class="btn btn-danger w-100">
          📄 Download PDF
        </a>
      </div>
      <?php endif; ?>
    </form>

    <hr>

    <?php
    if($result->num_rows > 0){
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead><tr>
                <th>ID</th>
                <th>Sicker Name</th>
                <th>Provider ID</th>
                <th>Type</th>
                <th>City</th>
                <th>Area</th>
                <th>Slot</th>
                <th>Request Date</th>
                <th>Status</th>
              </tr></thead><tbody>';
        while($row = $result->fetch_assoc()){
            // ✅ Format request_date to only date
            $date_only = date('Y-m-d', strtotime($row['request_date']));

            echo '<tr>
                    <td>'.$row['request_id'].'</td>
                    <td>'.$row['user_name'].'</td>
                    <td>'.$row['provider_id'].'</td>
                    <td>'.$row['request_type'].'</td>
                    <td>'.$row['city'].'</td>
                    <td>'.$row['area'].'</td>
                    <td>'.$row['slot'].'</td>
                    <td>'.$date_only.'</td>
                    <td>'.$row['status'].'</td>
                  </tr>';
        }
        echo '</tbody></table></div>';
    } else {
        if(!empty($from_date) && !empty($to_date))
            echo '<div class="alert alert-warning text-center">No requests found between '.$from_date.' and '.$to_date.'.</div>';
        else
            echo '<div class="alert alert-info text-center">Please select a date range to view report.</div>';
    }
    ?>
  </div>
</div>

</body>
</html>
