<?php
include 'db.php';
$result = $conn->query("SELECT * FROM requests");
?>
<h4>All Requests</h4>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>ID</th><th>Seeker ID</th><th>Provider ID</th><th>Date</th><th>Status</th>
    </tr> 
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['request_id'] ?></td>
      <td><?= $row['sicker_id'] ?></td>
      <td><?= $row['provider_id'] ?></td>
      <td><?= $row['request_date'] ?></td>
      <td><?= $row['status'] ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
