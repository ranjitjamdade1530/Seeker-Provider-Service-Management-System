<?php
include 'db.php';
$id = $_GET['id'] ?? 0;
$res = $conn->query("SELECT * FROM providers WHERE provider_id='$id'");
$p = $res && $res->num_rows > 0 ? $res->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Provider Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">

<h4>Provider Profile</h4>

<?php if ($p): ?>
<ul class="list-group mb-3">
  <li class="list-group-item"><b>ID:</b> <?= htmlspecialchars($p['provider_id']) ?></li>
  <li class="list-group-item"><b>Name:</b> <?= htmlspecialchars($p['provider_name']) ?></li>
  <li class="list-group-item"><b>Mobile:</b> <?= htmlspecialchars($p['provider_mobile']) ?></li>
  <li class="list-group-item"><b>Status:</b> <?= htmlspecialchars($p['status']) ?></li>
</ul>
<?php if (!empty($p['image']) && file_exists($p['image'])): ?>
  <div class="mb-3">
    <img src="<?= $p['image'] ?>" alt="Profile Photo" class="img-thumbnail" style="max-width: 200px; height: auto;">
  </div>  
<?php endif; ?>

<?php if (!empty($p['aadhar_doc'])): ?>
  <h5 class="mt-3 mb-2">Document</h5>
  <?php
    $doc = $p['aadhar_doc'];
    if (file_exists($doc)) {
        $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            echo "<img src='$doc' class='img-thumbnail' style='max-width:400px; height:auto;'>";
        } elseif ($ext === 'pdf') {
            echo "<div class='border rounded p-2' style='max-width:400px;'>";
            echo "<embed src='$doc' type='application/pdf' width='100%' height='400px'>";
            echo "</div>";
            echo "<p class='mt-2'><a href='$doc' target='_blank' class='btn btn-primary btn-sm'>Open Full PDF</a></p>";
        } else {
            echo "<a href='$doc' target='_blank' class='btn btn-secondary'>Download File</a>";
        }
    } else {
        echo "<p class='text-danger'>Document file not found in uploads/ folder.</p>";
    }
  ?>
<?php else: ?>
  <p class="text-warning">No document uploaded.</p>
<?php endif; ?>

<?php else: ?>
  <div class="alert alert-danger">Provider not found!</div>
<?php endif; ?>
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
