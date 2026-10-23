<?php
include 'db.php';
$message = "";

// Add service
if (isset($_POST['add_service'])) {
    $service_name = trim($_POST['service_name']);

    if (!empty($service_name)) {
        $stmt = $conn->prepare("INSERT INTO request_services (service_name) VALUES (?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $service_name);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Service added successfully.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Please enter a service name.</div>";
    }
}

// Update service
if (isset($_POST['update_service'])) {
    $id = $_POST['service_id'];
    $service_name = trim($_POST['service_name']);

    if (!empty($service_name)) {
        $stmt = $conn->prepare("UPDATE request_services SET service_name = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("si", $service_name, $id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-info'>Service updated successfully.</div>";
        }
    }
}

// Delete service
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM request_services WHERE id = $id");
    header("Location: manage_service_type.php");
    exit;
}

// Edit mode
$edit_service = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM request_services WHERE id = $id");
    if ($res->num_rows > 0) {
        $edit_service = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Request Service Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width: 550px;">
    <h3 class="mb-4">Manage Request Service Types</h3>

    <?= $message ?>

    <form method="POST" class="mb-4" style="width: 550px;" >
        <input type="hidden" name="service_id" value="<?= $edit_service['id'] ?? '' ?>">
        <div class="mb-3">
            <label for="service_name" class="form-label">Service Type</label>
            <input type="text" name="service_name" class="form-control"  required
                   value="<?= $edit_service['service_name'] ?? '' ?>">
        </div>

        <?php if ($edit_service): ?>
            <button type="submit" name="update_service" class="btn btn-info">Update</button>
            <a href="manage_service_type.php" class="btn btn-secondary">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_service" class="btn btn-primary">Add Service</button>
        <?php endif; ?>
    </form>

    <h5>Service Type List</h5>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Service Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $conn->query("SELECT * FROM request_services ORDER BY id DESC");
        if ($res->num_rows > 0):
            while ($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile;
        else: ?>
            <tr><td colspan="3" class="text-center">No services added.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
