<?php
session_start();
include 'db.php';

$sicker_id = $_SESSION['sicker_id'] ?? 1;
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;

if($year <= 0){
    echo '<div class="alert alert-warning">Invalid year.</div>';
    exit;
}

$stmt = $conn->prepare("SELECT * FROM requests WHERE sicker_id=? AND YEAR(request_date)=? ORDER BY request_date DESC");
$stmt->bind_param("ii", $sicker_id, $year);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    echo "<h5 class='text-center text-primary mb-3'>Report for Year $year</h5>";
    echo "<table class='table table-bordered table-striped'>
            <tr>
              <th>ID</th>
              <th>Provider</th>
              <th>Service</th>
              <th>Slot</th>
              <th>Date</th>
              <th>Status</th>
            </tr>";
    while($r = $res->fetch_assoc()){
        echo "<tr>
                <td>{$r['request_id']}</td>
                <td>".htmlspecialchars($r['provider_id'])."</td>
                <td>".htmlspecialchars($r['request_type'])."</td>
                <td>".htmlspecialchars($r['slot'])."</td>
                <td>".date('d-m-Y', strtotime($r['request_date']))."</td>
                <td>".htmlspecialchars($r['status'])."</td>
              </tr>";
    }
    echo "</table>";

    // PDF download button
    echo "<a href='sicker_yearly_requests.php?year=$year' class='btn btn-success' target='_blank'>
            <i class='bi bi-download'></i> Download PDF
          </a>";
} else {
    echo "<div class='alert alert-info'>No records found for $year.</div>";
}
?>
