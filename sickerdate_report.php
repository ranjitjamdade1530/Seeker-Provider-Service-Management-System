<?php
session_start();
require 'db.php';

if (!isset($_SESSION['sicker_id'])) {
    echo "<div class='alert alert-danger'>Session expired.</div>";
    exit;
}

$sicker_id = $_SESSION['sicker_id'];
$date = $_GET['report_date'] ?? date('Y-m-d');

// Fetch records for the selected date
$stmt = $conn->prepare("SELECT * FROM requests WHERE sicker_id=? AND request_date=? ORDER BY request_id DESC");
$stmt->bind_param("ss", $sicker_id, $date);
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="card shadow border-0">
  <div class="card-header bg-success text-white text-center">
    <h5>My Daily Report</h5>
  </div>
  <div class="card-body">
    <form id="reportForm">
      <div class="mb-3">
        <label for="report_date">Select Date</label>
        <input type="date" id="report_date" name="report_date" class="form-control" value="<?= $date ?>">
      </div>
      <button type="submit" class="btn btn-success">Generate Report</button>
    </form>
 
    <div id="reportResults" class="mt-3">
        <?php if ($res->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Provider</th>
                    <th>Service</th>
                    <th>Slot</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['request_id'] ?></td>
                    <td><?= htmlspecialchars($r['provider_id']) ?></td>
                    <td><?= htmlspecialchars($r['request_type']) ?></td>
                    <td><?= htmlspecialchars($r['slot']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Download PDF button -->
        <form method="post" action="sicker_download_requests.php" target="_blank">
            <input type="hidden" name="report_date" value="<?= htmlspecialchars($date) ?>">
            <button type="submit" class="btn btn-danger mt-2">Download PDF</button>
        </form>

        <?php else: ?>
            <div class="alert alert-info">No records found for <?= date('d-m-Y', strtotime($date)) ?>.</div>
        <?php endif; ?>
    </div>
  </div>
</div>

<script>
$('#reportForm').on('submit', function(e){
    e.preventDefault();
    const selectedDate = $('#report_date').val();

    // Reload the same tab via AJAX with new date
    $.get('sickerdate_report.php', { report_date: selectedDate }, function(html){
        const newResults = $(html).find('#reportResults').html();
        $('#reportResults').html(newResults); // replace old results
    });
});
</script>
