<?php
include 'db.php';

if (isset($_POST['city_name'])) {
    $city_name = $_POST['city_name'];

    $stmt = $conn->prepare("SELECT area_name FROM areas WHERE city_name = ?");
    $stmt->bind_param("s", $city_name);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">Select Area</option>';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['area_name'] . "'>" . $row['area_name'] . "</option>";
    }
}
?>
