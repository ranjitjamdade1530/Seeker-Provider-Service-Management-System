<?php
include 'db.php';
require('fpdf/fpdf.php'); // ✅ Ensure FPDF is available in your project (fpdf/fpdf.php)

// Handle gender filter
$gender = isset($_GET['gender']) ? $_GET['gender'] : 'All';

// Fetch data based on gender
if($gender == 'All'){
    $sql = "SELECT * FROM sickers ORDER BY sicker_id DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM sickers WHERE sicker_gender = ? ORDER BY sicker_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $gender);
}
$stmt->execute();
$result = $stmt->get_result();

// ✅ PDF Download Logic
if(isset($_GET['download']) && $_GET['download'] == 'pdf'){
    class PDF extends FPDF {
        function Header(){
            $this->SetFont('Arial','B',14);
            $this->Cell(0,10,'Seekers Report',0,1,'C');
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
    $pdf->Cell(0,10,"Gender: ".ucfirst($gender),0,1,'L');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(10,10,'ID',1);
    $pdf->Cell(40,10,'Name',1);
    $pdf->Cell(20,10,'Gender',1);
    $pdf->Cell(30,10,'Mobile',1);
    $pdf->Cell(30,10,'City',1);
    $pdf->Cell(30,10,'Area',1);
    $pdf->Cell(30,10,'Email',1);
    $pdf->Ln();

    // Table data
    $pdf->SetFont('Arial','',9);
    while($row = $result->fetch_assoc()){
        $pdf->Cell(10,8,$row['sicker_id'],1);
        $pdf->Cell(40,8,$row['sicker_name'],1);
        $pdf->Cell(20,8,$row['sicker_gender'],1);
        $pdf->Cell(30,8,$row['sicker_mobile'],1);
        $pdf->Cell(30,8,$row['city'],1);
        $pdf->Cell(30,8,$row['area'],1);
        $pdf->Cell(30,8,$row['email'],1);
        $pdf->Ln();
    }

    $pdf->Output('D', 'Sickers_Report_'.ucfirst($gender).'.pdf');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sickers Report</title>
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
    <h3 class="text-center mb-4">🧑‍🤝‍🧑 Seekers Report (By Gender)</h3>

    <form method="GET" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Select Gender</label>
        <select name="gender" class="form-select" onchange="this.form.submit()">
          <option value="All" <?php if($gender=='All') echo 'selected'; ?>>All</option>
          <option value="Male" <?php if($gender=='Male') echo 'selected'; ?>>Male</option>
          <option value="Female" <?php if($gender=='Female') echo 'selected'; ?>>Female</option>
        </select>
      </div>

      <div class="col-md-3 align-self-end">
        <a href="?gender=<?php echo $gender; ?>&download=pdf" class="btn btn-danger w-100">
          📄 Download PDF
        </a>
      </div>
    </form>

    <hr>

    <?php
    // Reset data for HTML table
    if($gender == 'All'){
        $sql = "SELECT * FROM sickers ORDER BY sicker_id DESC";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT * FROM sickers WHERE sicker_gender = ? ORDER BY sicker_id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $gender);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead><tr>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Mobile</th>
                <th>City</th>
                <th>Area</th>
                <th>Email</th>
              </tr></thead><tbody>';
        while($row = $result->fetch_assoc()){
            echo '<tr>
                    <td>'.$row['sicker_id'].'</td>
                    <td>'.$row['sicker_name'].'</td>
                    <td>'.$row['sicker_gender'].'</td>
                    <td>'.$row['sicker_mobile'].'</td>
                    <td>'.$row['city'].'</td>
                    <td>'.$row['area'].'</td>
                    <td>'.$row['email'].'</td>
                  </tr>';
        }
        echo '</tbody></table></div>';
    } else {
        echo '<div class="alert alert-warning text-center">No sickers found for selected gender.</div>';
    }
    ?>
  </div>
</div>

</body>
</html>
