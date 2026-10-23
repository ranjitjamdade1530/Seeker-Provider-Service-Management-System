<?php
header('Content-Type: application/json');
include 'db.php';
 
$provider_id = $_POST['provider_id'] ?? '';
$request_type = $_POST['request_type'] ?? '';

if (!$provider_id || !$request_type) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
} 

$stmt = $conn->prepare("SELECT SUM(rating) AS total, COUNT(*) AS count FROM requests WHERE provider_id = ? AND request_type = ?");
$stmt->bind_param("ss", $provider_id, $request_type);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['count'] > 0) {
    $avg = $result['total'] / $result['count'];
    $stars = str_repeat("★", round($avg)) . str_repeat("☆", 5 - round($avg));
    echo json_encode(['success' => true, 'stars' => $stars]);
} else {
    echo json_encode(['success' => false, 'message' => 'No ratings yet']);
}
