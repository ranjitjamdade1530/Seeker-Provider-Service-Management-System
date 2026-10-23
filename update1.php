<?php
include 'db.php'; // your database connection

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

if($id && $status){
    $stmt = $conn->prepare("UPDATE providers SET status=? WHERE provider_id=?");
    $stmt->bind_param("si", $status, $id);
    if($stmt->execute()){
        echo "Status updated successfully.";
    } else {
        echo "Failed to update status.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
