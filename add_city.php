<?php
// Database connection
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city = trim($_POST["city_name"]);

    if (!empty($city)) {
        $stmt = $conn->prepare("INSERT INTO cities (city_name) VALUES (?)");
        $stmt->bind_param("s", $city);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>City added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error adding city.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Please enter a city name.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add City</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width: 550px;">
    <h2 class="mb-4">Add City</h2>
    
    <?php echo $message; ?>
    
    <form method="POST" style="width: 550px;">
        <div class="mb-3">
            <label for="city_name" class="form-label">City Name</label>
            <input type="text" class="form-control" name="city_name" id="city_name" placeholder="Enter city name" required>
        </div>
        <center><button type="submit" class="btn btn-primary">Add City</button></center>
    </form>

</div>
</body>
</html>
