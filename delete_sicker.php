<?php
// Database connection
include 'db.php';

$sicker = [];
$showTable = false;

// Search sicker by ID
if (isset($_POST['search'])) {
    $sicker_id = $_POST['sicker_id'];

    $sql = "SELECT s.*, 
                   ts.time_range AS slot_name, 
                   rs.service_name AS request_type_name
            FROM sickers s
            LEFT JOIN time_slots ts ON s.slot = ts.id
            LEFT JOIN request_services rs ON s.request_type = rs.id
            WHERE s.sicker_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sicker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sicker = $result->fetch_assoc();
        $showTable = true;
    } else {
        echo "<script>alert('Sicker ID not found!');</script>";
    }
}

// Delete sicker
if (isset($_POST['delete'])) {
    $sicker_id = $_POST['sicker_id'];
    $sql = "DELETE FROM sickers WHERE sicker_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sicker_id);
    if ($stmt->execute()) {
        echo "<script>alert('Sicker deleted successfully!'); window.location.href='';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Sicker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center mb-4">Delete Seeker</h2>

    <!-- Search Form -->
    <form method="POST" class="mb-4 p-3 bg-white shadow rounded">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Enter Seeker ID</label>
                <input type="text" name="sicker_id" class="form-control" placeholder="e.g., S001" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="search" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <?php if ($showTable): ?>
    <!-- Display Sicker Details in Table -->
    <div class="p-4 bg-white shadow rounded">
        <table class="table table-bordered">
            <tr><th>ID</th><td><?= htmlspecialchars($sicker['sicker_id']) ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($sicker['sicker_name']) ?></td></tr>
            <tr><th>Age</th><td><?= htmlspecialchars($sicker['sicker_age']) ?></td></tr>
            <tr><th>Gender</th><td><?= htmlspecialchars($sicker['sicker_gender']) ?></td></tr>
            <tr><th>Mobile</th><td><?= htmlspecialchars($sicker['sicker_mobile']) ?></td></tr>
            <tr><th>City</th><td><?= htmlspecialchars($sicker['city']) ?></td></tr>
            <tr><th>Area</th><td><?= htmlspecialchars($sicker['area']) ?></td></tr>
            <tr><th>Request Date</th><td><?= htmlspecialchars($sicker['request_date']) ?></td></tr>
            <tr><th>Slot</th><td><strong><?= $sicker['slot_name'] ?? 'Not Found' ?></strong></td></tr>
            <tr><th>Request Type</th><td><strong><?= $sicker['request_type_name'] ?? 'Not Found' ?></strong></td></tr>
        </table>

        <!-- Delete Button -->
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this sicker?');">
            <input type="hidden" name="sicker_id" value="<?= $sicker['sicker_id'] ?>">
            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
        </form>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
