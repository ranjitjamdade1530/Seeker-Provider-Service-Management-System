<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $type = $_POST['type'];

    if ($type === 'sicker') {
        $table = 'sickers';
        $id_field = 'sicker_id';
    } elseif ($type === 'provider') {
        $table = 'providers';
        $id_field = 'provider_id';
    } else {
        die("Invalid type");
    }

    $result = $conn->query("SELECT status FROM $table WHERE $id_field = '$id'");
    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $newStatus = ($row['status'] === 'active') ? 'inactive' : 'active';
        $conn->query("UPDATE $table SET status = '$newStatus' WHERE $id_field = '$id'");
    }

    header("Location: admin_dashboard.php");
    exit;
}
