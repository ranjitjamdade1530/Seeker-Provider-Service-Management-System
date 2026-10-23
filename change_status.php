<?php
include 'db.php';

if(isset($_POST['id'], $_POST['type'], $_POST['status'])){
    $id = intval($_POST['id']);
    $type = $_POST['type'];
    $status = $_POST['status'];

    if($type == 'sicker'){
        $stmt = $conn->prepare("UPDATE sickers SET status=? WHERE sicker_id=?");
        $stmt->bind_param("si", $status, $id);
    } elseif($type == 'provider'){
        $stmt = $conn->prepare("UPDATE providers SET status=? WHERE provider_id=?");
        $stmt->bind_param("si", $status, $id);
    }

    if($stmt->execute()){
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Error updating status.";
    }
}
?>
