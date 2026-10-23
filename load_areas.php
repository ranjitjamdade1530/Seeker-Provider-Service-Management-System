<?php
include 'db.php';

if (isset($_GET['city_id'])) {
    $city_id = $_GET['city_id'];

    // First, get the city name from the cities table using city_id
    $cityStmt = $conn->prepare("SELECT city_name FROM cities WHERE id = ?");
    $cityStmt->bind_param("i", $city_id);
    $cityStmt->execute();
    $cityResult = $cityStmt->get_result();

    if ($cityRow = $cityResult->fetch_assoc()) {
        $city_name = $cityRow['city_name'];

        // Now fetch areas where city_name matches
        $stmt = $conn->prepare("SELECT * FROM areas WHERE city_name = ?");
        $stmt->bind_param("s", $city_name);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<option value="">Select Area</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($row['area_name']) . '">' . htmlspecialchars($row['area_name']) . '</option>';
        }
    } else {
        echo '<option value="">No city found</option>';
    }
}
?>
