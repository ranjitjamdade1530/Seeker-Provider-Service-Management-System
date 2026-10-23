<?php
session_start();
require 'db.php';

if (!isset($_SESSION['sicker_id'])) {
    echo "<div class='alert alert-danger'>Session expired.</div>";
    exit;
}

$sicker_id = $_SESSION['sicker_id'];
$month = $_GET['month'] ?? date('Y-m');
?>

<div class="card shadow border-0">
  <div class="card-header bg-success text-white text-center">
    <h5>My Monthly Report</h5>
  </div>
  <div class="card-body">
    <form id="monthReportForm">
      <div class="mb-3">
        <label for="month">Select Month</label>
        <input type="month" id="month" name="month" class="form-control" value="<?= $month ?>" required>
      </div>
      <button type="submit" class="btn btn-success">Generate Report</button>
    </form>

    <div id="monthReportResults" class="mt-3">
        <?php
        $stmt = $conn->prepare("SELECT * FROM requests WHERE sicker_id=? AND DATE_FORMAT(request_date,'%Y-%m')=? ORDER BY request_date DESC");
        $stmt->bind_param("ss", $sicker_id, $month);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0):
        ?>
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Provider</th>
                    <th>Service</th>
                    <th>Slot</th>
                    <th>Date</th>
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
                    <td><?= date('d-m-Y', strtotime($r['request_date'])) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Download PDF button -->
        <form method="post" action="sicker_monthly_requests.php" target="_blank">
            <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">
            <button type="submit" class="btn btn-danger mt-2">
                <i class="bi bi-download"></i> Download PDF
            </button>
        </form>

        <?php else: ?>
            <div class="alert alert-info">No records found for <?= htmlspecialchars($month) ?>.</div>
        <?php endif; ?>
    </div>
  </div>
</div>

<script>
$('#monthReportForm').on('submit', function(e){
    e.preventDefault();
    const selectedMonth = $('#month').val();

    // Reload the same tab via AJAX with new month
    $.get('simonth.php', { month: selectedMonth }, function(html){
        const newResults = $(html).find('#monthReportResults').html();
        $('#monthReportResults').html(newResults); // replace old results
    });
});
</script>
