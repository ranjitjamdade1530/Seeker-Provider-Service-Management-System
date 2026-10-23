<?php
include 'db.php';
$id = $_GET['id'] ?? 0;
$res = $conn->query("SELECT * FROM sickers WHERE sicker_id='$id'");
$s = $res && $res->num_rows > 0 ? $res->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Seeker Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-dark text-white">
      <h4 class="mb-0">Seeker Profile</h4>
    </div>
    <div class="card-body">

      <?php if ($s): ?>
        <ul class="list-group">
          <li class="list-group-item"><b>ID:</b> <?= htmlspecialchars($s['sicker_id']) ?></li>
          <li class="list-group-item"><b>Name:</b> <?= htmlspecialchars($s['sicker_name']) ?></li>
          <li class="list-group-item"><b>Mobile:</b> <?= htmlspecialchars($s['sicker_mobile']) ?></li>
          <li class="list-group-item"><b>Status:</b> <?= htmlspecialchars($s['status']) ?></li>
          <li class="list-group-item">
   <!-- Image Display -->
<li class="list-group-item">
  <b>Image:</b><br>
  <?php
    $imagePath = $s['image'] ?? '';
    if (!empty($imagePath) && file_exists($imagePath)) {
        echo '<img src="' . htmlspecialchars($imagePath) . '" 
                   alt="Seeker Image" 
                   class="img-thumbnail mt-2" 
                   style="max-width:200px; height:auto;">';
    } else {
        echo '<span class="text-muted">No image uploaded</span>';
    }
  ?> 
</li>


<!-- Aadhar Document Display -->
<!-- Aadhar Document Display -->
<?php if (!empty($s['aadhar_doc'])): ?>
  <div class="mt-4">
    <h5 class="mb-2">Aadhar Document</h5>
    <?php
      $doc = $s['aadhar_doc'];
      if (file_exists($doc)) {
          echo "<div class='border rounded p-2' style='max-width:400px;'>";
          echo "<embed src='" . htmlspecialchars($doc) . "' type='application/pdf' width='100%' height='400px'>";
          echo "</div>";
          echo "<p class='mt-2'><a href='" . htmlspecialchars($doc) . "' target='_blank' class='btn btn-primary btn-sm'>Open Full PDF</a></p>";
      } else {
          echo "<p class='text-danger'>Aadhar file not found in uploads/ folder.</p>";
      }
    ?>
  </div>
<?php endif; ?>




           

      <?php else: ?>
        <div class="alert alert-danger">Seeker not found!</div>
      <?php endif; ?>

    </div>
  </div>
</div>
<p class="mt-3">
  <a href="admin_dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
</p>

<script>
  // Redirect with tab hash to open Active Account tab directly
  document.querySelector('a[href="admin_dashboard.php"]').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = "activeaccount.php"; // handled in JS by sidebar load
    if (window.parent !== window) {
      window.parent.postMessage({ action: "loadTab", tab: "Active Account" }, "*");
    }
  });
</script>
</body> 
</html>
