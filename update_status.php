<?php
include 'db.php'; // your DB config file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    $sql = "UPDATE requests SET status = ? WHERE request_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $request_id);

    if ($stmt->execute()) {
        echo "Status updated to '$status'.";
    } else {
        echo "Error updating status.";
    }
} else {
    echo "Invalid request.";
}
?>
  