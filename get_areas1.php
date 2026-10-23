<?php
require 'db.php';
if(isset($_POST['city_name'])){
    $city_name = $conn->real_escape_string($_POST['city_name']);
    $result = $conn->query("SELECT id, area_name FROM areas WHERE city_name='$city_name' ORDER BY area_name ASC");
    echo '<option value="">Select Area</option>';
    while($row=$result->fetch_assoc()){
        echo '<option value="'.htmlspecialchars($row['area_name']).'">'.htmlspecialchars($row['area_name']).'</option>';
    }
}
