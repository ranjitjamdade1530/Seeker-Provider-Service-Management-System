<?php
session_start();
require 'db.php';

if(!isset($_POST['request_id'], $_POST['rating'])){
    echo json_encode(['success'=>false,'message'=>'Invalid data']);
    exit;
} 

$request_id = intval($_POST['request_id']);
$rating = intval($_POST['rating']);
$time_spent = isset($_POST['time_spent']) ? floatval($_POST['time_spent']) : null;

$stmt = $conn->prepare("UPDATE requests SET rating=?, time_spent=? WHERE request_id=?");
$stmt->bind_param("idi", $rating, $time_spent, $request_id);

if($stmt->execute()){
    echo json_encode(['success'=>true, 'message'=>'Rating & Time Spent saved']);
} else {
    echo json_encode(['success'=>false, 'message'=>'Update failed']);
}
