<?php
session_start();
include 'db.php';

// ✅ Simulated login for testing (remove later)
if (!isset($_SESSION['provider_id'])) {
    $_SESSION['provider_id'] = 'P001'; // Example provider ID
}

// Fetch provider details
$provider_id = $_SESSION['provider_id'];
$sql = "SELECT provider_name, provider_dob FROM providers WHERE provider_id='$provider_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Provider not found!");
}
$row = $result->fetch_assoc();
$name = $row['provider_name'];
$dob = $row['provider_dob'];
 
// ✅ Calculate Age
$from = new DateTime($dob);
$to   = new DateTime('today');
$age = $from->diff($to)->y;

// ✅ Years left until 60
$years_left = max(0, 60 - $age);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provider Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .card { max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); text-align: center; }
    h2 { color: #007bff; margin-bottom: 20px; }
    .countdown { font-size: 1.3rem; font-weight: bold; color: #28a745; margin-top: 20px; }
    .warning { font-size: 1.2rem; font-weight: bold; color: red; margin-top: 20px; }
  </style>
</head>
<body>
<div class="card">
    <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
    <p><strong>Age:</strong> <?= $age ?> years</p>

    <?php if ($age < 60): ?>
        <div class="countdown">
            ✅ You have <span id="years_left"><?= $years_left ?></span> days left for apply as a Seeker.
        </div>
    <?php else: ?>
        <div class="warning">
            ❌ You are not eligible to give services as a provider.
        </div>
    <?php endif; ?>
</div>

<script>
// Optional countdown that decreases every year (simulation)
let yearsLeft = <?= $years_left ?>;
if (yearsLeft > 0) {
    const el = document.getElementById("years_left");
    setInterval(() => {
        if (yearsLeft > 0) {
            yearsLeft--;
            el.textContent = yearsLeft;
        }
    }, 5000); // demo: decrease every 5 seconds (instead of every year 😅)
}
</script>
</body>
</html>
