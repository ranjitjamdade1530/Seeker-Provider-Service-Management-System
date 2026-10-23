<?php
require 'db.php';
header('Content-Type: application/json');

if (!isset($_POST['city']) || empty($_POST['city'])) {
    echo json_encode(['success' => false, 'areas' => []]);
    exit;
}

$city = $_POST['city'];

$stmt = $conn->prepare("SELECT area_name FROM areas WHERE city_name = ? ORDER BY area_name");
$stmt->bind_param("s", $city);
$stmt->execute();
$res = $stmt->get_result();

$areas = [];
while ($row = $res->fetch_assoc()) {
    $areas[] = $row['area_name'];
}

echo json_encode([
    'success' => count($areas) > 0,
    'areas' => $areas
]);
