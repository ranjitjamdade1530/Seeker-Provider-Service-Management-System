<?php
include 'db.php';

$message = "";

// Add Time Slot
if (isset($_POST['add_slot'])) {
    $time_range = trim($_POST['time_range']);

    if (!empty($time_range)) {
        $stmt = $conn->prepare("INSERT INTO time_slots (time_range) VALUES (?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error); // Show real error if table/column is wrong
        }
        $stmt->bind_param("s", $time_range);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Time slot added successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add time slot.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Please enter a time range.</div>";
    }
}

// Update Time Slot
if (isset($_POST['update_slot'])) {
    $slot_id = $_POST['slot_id'];
    $time_range = trim($_POST['time_range']);

    if (!empty($time_range)) {
        $stmt = $conn->prepare("UPDATE time_slots SET time_range = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("si", $time_range, $slot_id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-info'>Time slot updated successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to update time slot.</div>";
        }
    }
}

// Delete Time Slot
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM time_slots WHERE id = $id");
    header("Location: manage_slot.php");
    exit;
}

// Edit Mode
$edit_slot = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM time_slots WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $edit_slot = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Time Slots</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width: 550px;">
    <h3 class="mb-4"> Time Slot Entry </h3>

    <?= $message ?>

    <!-- Form -->
    <form method="POST" class="mb-4" style="width: 550px;">
        <input type="hidden" name="slot_id" value="<?= $edit_slot['id'] ?? '' ?>">

        <div class="mb-3">
            <label for="time_range" class="form-label">Time Range</label>
            <input type="text" class="form-control" name="time_range" placeholder="e.g. 9 to 12" required
                   value="<?= $edit_slot['time_range'] ?? '' ?>">
        </div>

        <?php if ($edit_slot): ?>
            <button type="submit" name="update_slot" class="btn btn-info">Update Slot</button>
            <a href="manage_slot.php" class="btn btn-secondary">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_slot" class="btn btn-primary">Add Slot</button>
        <?php endif; ?>
    </form>

    <!-- Slot List -->
    <h5>Time Slot List</h5>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Time Range</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $conn->query("SELECT * FROM time_slots ORDER BY id DESC");
        if ($res->num_rows > 0):
            while ($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['time_range']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this slot?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile;
        else: ?>
            <tr><td colspan="3" class="text-center">No time slots added.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
