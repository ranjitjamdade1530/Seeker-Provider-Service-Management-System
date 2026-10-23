<?php
include 'db.php';
$message = "";

// === Add City ===
if (isset($_POST['add_city'])) {
    $city = trim($_POST['city_name']);
    if (!empty($city)) {
        $stmt = $conn->prepare("INSERT INTO cities (city_name) VALUES (?)");
        $stmt->bind_param("s", $city);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>City added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add city.</div>";
        }
    }
}

// === Update City ===
if (isset($_POST['update_city'])) {
    $id = $_POST['city_id'];
    $city = trim($_POST['city_name']);
    if (!empty($city)) {
        $stmt = $conn->prepare("UPDATE cities SET city_name = ? WHERE id = ?");
        $stmt->bind_param("si", $city, $id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-info'>City updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to update city.</div>";
        }
    }
}

// === Delete City ===
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM cities WHERE id = $id");
    header("Location: manage_city.php");
    exit;
}

// === Edit City (fill form) ===
$edit_city = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM cities WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_city = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Cities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width:600px;">
    <h3 class="mb-4">City Management </h3>

    <?= $message ?>

    <form method="POST" class="mb-4" style="width:550px;">
        <input type="hidden" name="city_id" value="<?= $edit_city['id'] ?? '' ?>">
        <div class="mb-3">
            <label for="city_name" class="form-label">City Name</label>
            <input type="text" class="form-control" name="city_name" id="city_name"
                   value="<?= $edit_city['city_name'] ?? '' ?>" required>
        </div>
        <?php if ($edit_city): ?>
            <button type="submit" name="update_city" class="btn btn-info">Update City</button>
            <a href="manage_city.php" class="btn btn-secondary">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_city" class="btn btn-primary">Add City</button>
        <?php endif; ?>
    </form>

    <h4>City List</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>City Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $conn->query("SELECT * FROM cities ORDER BY id DESC");
        if ($res->num_rows > 0):
            while ($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['city_name'] ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= $row['id'] ?>"
                           onclick="return confirm('Are you sure you want to delete this city?')"
                           class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endwhile;
        else: ?>
            <tr><td colspan="3" class="text-center">No cities found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
