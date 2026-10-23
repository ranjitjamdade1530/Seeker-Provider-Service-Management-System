<?php
include 'db.php'; // your DB connection

if(isset($_POST['request_id']) && isset($_POST['status'])){
    $id = intval($_POST['request_id']);
    $status = $_POST['status'];

    // Optional: Validate allowed statuses
    $allowed_statuses = ['Pending', 'Accepted', 'Rejected'];
    if(!in_array($status, $allowed_statuses)){
        echo 'invalid_status';
        exit;
    }

    // Update in DB
    $stmt = $conn->prepare("UPDATE requests SET status=? WHERE request_id=?");
    $stmt->bind_param("si", $status, $id);

    if($stmt->execute()){
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
} else {
    echo 'invalid_data';
}
?>
