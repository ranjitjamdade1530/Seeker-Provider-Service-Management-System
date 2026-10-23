<?php
include 'db.php';

if(isset($_POST['city'])){
    $city = $_POST['city'];
    // Fetch areas belonging to this city
    $stmt = $conn->prepare("SELECT area_name FROM areas WHERE city_name=? ORDER BY area_name ASC");
    $stmt->bind_param("s", $city);
    $stmt->execute();
    $result = $stmt->get_result();
    echo '<option value="">--Select Area--</option>';
    while($row = $result->fetch_assoc()){
        echo '<option value="'.htmlspecialchars($row['area_name']).'">'.htmlspecialchars($row['area_name']).'</option>';
    }
}
?>
