<?php
// Show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
include 'db.php';

// Fetch only converted providers
$providers = $conn->query("SELECT * FROM providers WHERE status='converted' ORDER BY provider_id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Converted Providers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">Converted Providers and Their Sicker Info</h3>

    <?php if($providers->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Provider ID</th>
                    <th>Provider Name</th>
                    <th>Mobile</th>
                    <th>Sicker ID</th>
                    <th>Sicker Name</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $providers->fetch_assoc()): ?>
                    <?php
                    // Get corresponding sicker info for this provider
                    $mobile = $conn->real_escape_string($p['provider_mobile']);
                    $s = $conn->query("SELECT * FROM sickers WHERE sicker_mobile='$mobile' ORDER BY id DESC LIMIT 1");
                    $sicker_id = $sicker_name = $sicker_password = '';
                    if($s && $s->num_rows > 0){
                        $sc = $s->fetch_assoc();
                         $sicker_id = $sc['sicker_id'];
                        $sicker_name = $sc['sicker_name'];
                        $sicker_password = $sc['password'];
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['provider_id']); ?></td>
                        <td><?php echo htmlspecialchars($p['provider_name']); ?></td>
                        <td><?php echo htmlspecialchars($p['provider_mobile']); ?></td>
                        <td><?php echo htmlspecialchars($sicker_id); ?></td>
                        <td><?php echo htmlspecialchars($sicker_name); ?></td>
                        <td><?php echo htmlspecialchars($sicker_password); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-info">No converted providers found.</div>
    <?php endif; ?>
</div>
</body>
</html>
