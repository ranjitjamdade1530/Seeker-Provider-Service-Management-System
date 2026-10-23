<?php
session_start();
include 'db.php';

// Optional: You can restrict access if needed
// if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }

$result = $conn->query("SELECT * FROM emergency_conversions ORDER BY request_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Emergency Conversion Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">Provider to Sicker Emergency Conversion Requests</h3>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Provider ID</th>
                            <th>Reason</th>
                            <th>Certificate</th>
                            <th>Request Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= htmlspecialchars($row['provider_id']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['reason'])) ?></td>
                                <td>
                                    <?php if (file_exists($row['certificate_path'])): ?>
                                        <a href="<?= $row['certificate_path'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                            View Certificate
                                        </a>
                                    <?php else: ?>
                                        <span class="text-danger">Not Found</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y h:i A', strtotime($row['request_date'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No emergency requests found.</div>
    <?php endif; ?>
</div>
</body>
</html>
