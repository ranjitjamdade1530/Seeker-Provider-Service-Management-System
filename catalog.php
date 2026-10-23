<?php
session_start();
require 'db.php';

if(!isset($_SESSION['sicker_id'])){ die("Unauthorized"); }
$sicker_id = $_SESSION['sicker_id'];

$total_allowed = 10;
 
// Fetch only accepted requests
$stmt = $conn->prepare("
    SELECT r.request_id, r.request_type, r.time_spent, r.city, r.area, r.slot, r.provider_id, p.provider_name
    FROM requests r
    JOIN providers p ON r.provider_id = p.provider_id
    WHERE r.sicker_id=? AND r.status='Accepted'
    ORDER BY r.request_date DESC
");
$stmt->bind_param("s", $sicker_id);
$stmt->execute();
$result = $stmt->get_result();

$used_hours = 0;
$requests = [];
while($row = $result->fetch_assoc()){
    $requests[] = $row;
    $used_hours += floatval($row['time_spent'] ?? 0);
}

$remaining_hours = max(0, $total_allowed - $used_hours);
?>

<div class="card shadow">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-list"></i> Accepted Services
  </div>
  <div class="card-body">

    <div class="alert alert-info">
      <strong>Total Hours:</strong> <?= $total_allowed ?> <br>
      <strong>Used Hours:</strong> <?= $used_hours ?> <br>
      <strong>Remaining Hours:</strong> <?= $remaining_hours ?>
    </div>

    <?php if(count($requests) > 0): ?>
      <div class="list-group">
        <?php foreach($requests as $row): ?>
          <div class="list-group-item">
            <h5><?= htmlspecialchars($row['request_type']) ?></h5>
            <p>
              <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($row['city']) ?> - <?= htmlspecialchars($row['area']) ?><br>
              <i class="bi bi-clock"></i> Slot: <?= htmlspecialchars($row['slot']) ?><br>
              <i class="bi bi-person"></i> Provider: <?= htmlspecialchars($row['provider_name']) ?><br>
              <i class="bi bi-person"></i> Provider Id: <?= htmlspecialchars($row['provider_id']) ?>

            </p>

            <p>
              <strong>Time Spent (hours):</strong> <?= htmlspecialchars($row['time_spent'] ?? 0) ?>
            </p>

          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning">No accepted requests yet.</div>
    <?php endif; ?>

  </div>
</div>
