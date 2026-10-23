<?php
session_start();
require 'db.php';

if (!isset($_SESSION['sicker_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$sicker_id = $_SESSION['sicker_id'];

$stmt = $conn->prepare("SELECT SUM(time_spent) as total_hours 
                        FROM requests 
                        WHERE sicker_id=? AND status='Accepted'");
$stmt->bind_param("s", $sicker_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$total_hours = $res['total_hours'] ?? 0;

if($total_hours >= 10){
    echo json_encode(['success' => false, 'message' => 'You have already completed 10 hours of service.']);
} else {
    echo json_encode(['success' => true, 'remaining' => 10 - $total_hours]);
}
?>