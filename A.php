<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Providers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
  <h4>All Providers</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Mobile</th>
          <th>Password</th>
          <th>View</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $providers = $conn->query("SELECT * FROM providers ORDER BY provider_id ASC");
        while ($p = $providers->fetch_assoc()):
          $pid = htmlspecialchars($p['provider_id']);
        ?>
        <tr>
          <td><?php echo $pid; ?></td>
          <td><?php echo htmlspecialchars($p['provider_name']); ?></td>
          <td><?php echo htmlspecialchars($p['provider_mobile']); ?></td>
          <td><?php echo htmlspecialchars($p['password']); ?></td>
          <td><a href="view_provider.php?id=<?php echo urlencode($pid); ?>" class="btn btn-sm btn-info mb-1">View</a></td>
          <td>
          <select 
  class="form-select form-select-sm status-dropdown"
  data-id="<?php echo htmlspecialchars($p['provider_id']); ?>"
  data-type="provider">
  <option value="pending" <?php if($p['status']=='pending') echo 'selected'; ?>>Pending</option>
  <option value="active" <?php if($p['status']=='active') echo 'selected'; ?>>Active</option>
  <option value="inactive" <?php if($p['status']=='inactive') echo 'selected'; ?>>Inactive</option>
  <option value="converted" <?php if($p['status']=='converted') echo 'selected'; ?>>Converted as Seeker</option>
</select>

            <span class="text-success small msg-updated" style="display:none;">Updated!</span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('change', function(e) {
    const sel = e.target.closest('.status-dropdown');
    if (!sel) return;

    const id = sel.dataset.id;
    const type = sel.dataset.type;
    const status = sel.value;

    if (!id || !type || !status) return;

    if (status === 'converted') {
        if (!confirm('Convert this provider to a seeker?')) return;
    }

    sel.disabled = true;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('type', type);
    formData.append('status', status);

    fetch('update_request_status.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        alert(text); // TEMP: Show result
        if (text.trim() === 'success') {
            sel.classList.add('is-valid');
            setTimeout(() => sel.classList.remove('is-valid'), 1500);
        } else {
            sel.classList.add('is-invalid');
            setTimeout(() => sel.classList.remove('is-invalid'), 1500);
        }
    })
    .catch(err => {
        alert('Error: ' + err);
    })
    .finally(() => sel.disabled = false);
});
</script>


</body>
</html>
