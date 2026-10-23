<?php
session_start();
require 'db.php';
$sicker_id = $_SESSION['sicker_id'];

$stmt = $conn->prepare("SELECT * FROM requests WHERE sicker_id=? ORDER BY request_date DESC");
$stmt->bind_param("s", $sicker_id);
$stmt->execute();
$result = $stmt->get_result();
?>
  
<h3>Your Requests</h3>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Provider</th>
      <th>Service</th>
      <th>City</th>
      <th>Area</th> 
      <th>Slot</th>
      <th>Status</th>
      <th>Rating</th>
      <th>Time Spent (hrs)</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr id="row-<?= $row['request_id'] ?>">
  <td><?= htmlspecialchars($row['provider_id']) ?></td>
  <td><?= htmlspecialchars($row['request_type']) ?></td>
  <td><?= htmlspecialchars($row['city']) ?></td>
  <td><?= htmlspecialchars($row['area']) ?></td>
  <td><?= htmlspecialchars($row['slot']) ?></td>
  <td><?= htmlspecialchars($row['status']) ?></td>
  <td>
    <select class="form-select rating-field" data-id="<?= $row['request_id'] ?>" required <?= !empty($row['rating']) ? 'disabled' : '' ?>>
      <option value="">Rate</option>
      <?php for($i=1;$i<=5;$i++): ?>
        <option value="<?= $i ?>" <?= ($row['rating']==$i ? "selected":"") ?>><?= $i ?> ★</option>
      <?php endfor; ?>
    </select>
  </td>
  <td>
    <input type="number" step="0.5" min="0" class="form-control time-field"
           data-id="<?= $row['request_id'] ?>"
           value="<?= !empty($row['time_spent']) ? htmlspecialchars($row['time_spent']) : '' ?>"
           required <?= !empty($row['time_spent']) ? 'disabled' : '' ?>>
  </td>
  <td>
    <button class="btn btn-success btn-save" data-id="<?= $row['request_id'] ?>" <?= (!empty($row['rating']) || !empty($row['time_spent'])) ? 'disabled' : '' ?>>Save</button>
  </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
  $('.btn-save').click(function(){
    var requestId = $(this).data('id');
    var ratingField = $('#row-' + requestId + ' .rating-field');
    var timeField = $('#row-' + requestId + ' .time-field');
    var rating = ratingField.val();
    var timeSpent = timeField.val();
    var button = $(this);

    if(rating === "" || timeSpent === ""){
      alert("Please fill both rating and time spent!");
      return;
    }

    $.ajax({
      url: 'save_request_data.php',
      method: 'POST',
      data: {
        request_id: requestId,
        rating: rating,
        time_spent: timeSpent
      },
      success: function(response){
        if(response === 'success'){
          alert("Saved successfully!");

          // Update inputs with saved data
          ratingField.val(rating).prop('disabled', true);
          timeField.val(timeSpent).prop('disabled', true);
          button.prop('disabled', true);
        } else {
          alert("Error saving data: " + response);
        }
      }
    });
  });
});
</script>


