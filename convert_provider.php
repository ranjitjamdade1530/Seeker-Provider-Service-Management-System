<?php
require 'db.php';
session_start();

if(!isset($_GET['provider_id'])) die("Invalid request");

$provider_id = $_GET['provider_id'];

// Fetch provider
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id=? AND converted=0");
$stmt->bind_param("s", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$provider) die("Provider not found or already converted");

// Calculate age
$age = (new DateTime($provider['provider_dob']))->diff(new DateTime('today'))->y;

// Generate Sicker ID
$lastSicker = $conn->query("SELECT sicker_id FROM sickers ORDER BY id DESC LIMIT 1");
$number = ($lastSicker->num_rows > 0) ? intval(substr($lastSicker->fetch_assoc()['sicker_id'],1)) + 1 : 1;
$new_sicker_id = 'S' . str_pad($number,3,'0',STR_PAD_LEFT);

// Insert into sickers// Prepare values
$status = 'active';
$email  = $provider['email'] ?? ''; // if email exists in provider table

// Insert into sickers
$stmt = $conn->prepare("INSERT INTO sickers 
(sicker_id, sicker_name, dob, sicker_age, sicker_gender, sicker_mobile, city, area, request_date, password, image, aadhar_doc, status, email)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    "sssissssssssss",
    $new_sicker_id,
    $provider['provider_name'],
    $provider['provider_dob'],
    $age,
    $provider['provider_gender'],
    $provider['provider_mobile'],
    $provider['city'],
    $provider['area'],
    $provider['date'],
    $provider['password'],
    $provider['image'],
    $provider['aadhar_doc'],
    $status,
    $email
);

$stmt->execute();
$stmt->close();

// Update provider status
$conn->query("UPDATE providers SET converted=1, status='converted' WHERE provider_id='$provider_id'");

// Display converted info
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Provider Converted</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-success">Provider converted successfully!</div>
    <table class="table table-bordered">
        <tr><th>Old Provider ID</th><td><?= $provider['provider_id'] ?></td></tr>
        <tr><th>New Sicker ID</th><td><?= $new_sicker_id ?></td></tr>
        <tr><th>Name</th><td><?= $provider['provider_name'] ?></td></tr>
        <tr><th>Password</th><td><?= $provider['password'] ?></td></tr>
    </table>
    <a href="activeaccount.php" class="btn btn-primary">Back to Providers List</a>
</div>
</body>
</html>
