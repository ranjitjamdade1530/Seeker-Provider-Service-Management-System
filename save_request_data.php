<?php
require 'db.php';

if(isset($_POST['request_id'], $_POST['rating'], $_POST['time_spent'])){
    $request_id = $_POST['request_id'];
    $rating = $_POST['rating'];
    $time_spent = $_POST['time_spent'];

    $stmt = $conn->prepare("UPDATE requests SET rating=?, time_spent=? WHERE request_id=?");
    $stmt->bind_param("ddi", $rating, $time_spent, $request_id);

    if($stmt->execute()){
        echo "success";
    } else {
        echo $conn->error;
    }
}
?>
