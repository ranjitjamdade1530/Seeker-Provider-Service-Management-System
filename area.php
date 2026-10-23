<?php
include 'db.php';

$message = "";

// Add Area (Save city name instead of ID)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city_id = $_POST["city_id"];
    $area_name = trim($_POST["area_name"]);

    if (!empty($city_id) && !empty($area_name)) {
        // Get city name from selected city ID
        $city_stmt = $conn->prepare("SELECT city_name FROM cities WHERE id = ?");
        $city_stmt->bind_param("i", $city_id);
        $city_stmt->execute();
        $city_stmt->bind_result($city_name);
        $city_stmt->fetch();
        $city_stmt->close();

        // Insert into areas table
        $stmt = $conn->prepare("INSERT INTO areas (area_name, city_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $area_name, $city_name);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Area added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error adding area.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Please select a city and enter area name.</div>";
    }
}

// Fetch Cities for Dropdown
$cities = [];
$result = $conn->query("SELECT * FROM cities ORDER BY city_name ASC");
while ($row = $result->fetch_assoc()) {
    $cities[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Area</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width: 550px;">
    <h3 class="mb-4">Add Area (Based on City)</h3>

    <?= $message ?>

    <form method="POST" style="width: 550px;">
        <div class="mb-3">
            <label for="city_id" class="form-label">Select City</label>
            <select name="city_id" id="city_id" class="form-select" required>
                <option value="">-- Select City --</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?= $city['id'] ?>"><?= htmlspecialchars($city['city_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="area_name" class="form-label">Area Name</label>
            <input type="text" class="form-control" name="area_name" id="area_name" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Area</button>
    </form>

    <hr>

    <h5>All Areas (With City)</h5>
    <table class="table table-bordered table-striped mt-3">
        <thead>
        <tr>
            <th>ID</th>
            <th>Area Name</th>
            <th>City</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $conn->query("SELECT id, area_name, city_name FROM areas ORDER BY id DESC");
        if ($res->num_rows > 0):
            while ($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['area_name']) ?></td>
                    <td><?= htmlspecialchars($row['city_name']) ?></td>
                </tr>
            <?php endwhile;
        else: ?>
            <tr><td colspan="3" class="text-center">No areas found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
