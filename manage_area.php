<?php
include 'db.php';

$message = "";

// ===== Update Area =====
if (isset($_POST['update_area'])) {
    $id = $_POST['area_id'];
    $area_name = trim($_POST['area_name']);
    $city_name = $_POST['city_name']; // Not city_id

    if (!empty($area_name) && !empty($city_name)) {
        $stmt = $conn->prepare("UPDATE areas SET area_name = ?, city_name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $area_name, $city_name, $id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Area updated successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error updating area.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Please enter area name and select city.</div>";
    }
}

// ===== Delete Area =====
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM areas WHERE id = $id");
    header("Location: manage_area.php");
    exit;
}

// ===== Edit Mode =====
$edit_area = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM areas WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_area = $result->fetch_assoc();
    }
}

// Get cities for dropdown
$cities = [];
$city_query = $conn->query("SELECT * FROM cities ORDER BY city_name ASC");
while ($row = $city_query->fetch_assoc()) {
    $cities[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update / Delete Area</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width:600px;">
    <h3>Update / Delete Area</h3>
    <?= $message ?>

    <?php if ($edit_area): ?>
        <form method="POST" class="mb-4" style="width:550px;">
            <input type="hidden" name="area_id" value="<?= $edit_area['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Area Name</label>
                <input type="text" class="form-control" name="area_name" value="<?= htmlspecialchars($edit_area['area_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">City</label>
                <select name="city_name" class="form-select" required>
                    <option value="">-- Select City --</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= htmlspecialchars($city['city_name']) ?>" <?= ($city['city_name'] == $edit_area['city_name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($city['city_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="update_area" class="btn btn-primary">Update Area</button>
            <a href="manage_area.php" class="btn btn-secondary">Cancel</a>
        </form>
    <?php endif; ?>

    <h5>All Areas</h5>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Area Name</th>
            <th>City Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $conn->query("SELECT id, area_name, city_name FROM areas ORDER BY id DESC");
        if ($res && $res->num_rows > 0):
            while ($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['area_name']) ?></td>
                    <td><?= htmlspecialchars($row['city_name']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this area?')" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endwhile;
        else: ?>
            <tr><td colspan="4" class="text-center">No areas found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
