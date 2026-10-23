<?php
require 'db.php';

if(isset($_POST['city_name'])){
    $city = $conn->real_escape_string($_POST['city_name']);
    $areas = $conn->query("SELECT area_name FROM areas WHERE city_name='$city' ORDER BY area_name ASC");

    echo '<option value="">Select Area</option>';
    while($a = $areas->fetch_assoc()){
        echo '<option value="'.htmlspecialchars($a['area_name']).'">'.htmlspecialchars($a['area_name']).'</option>';
    }
}
?>