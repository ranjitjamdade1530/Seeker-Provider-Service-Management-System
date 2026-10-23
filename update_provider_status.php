<?php
include 'db.php';

$provider_id = $_POST['provider_id'] ?? '';
$status = $_POST['status'] ?? '';

if(!$provider_id || !$status){
    echo 'Invalid request';
    exit;
}

if($status === 'converted'){
    // Get provider details
    $provider = $conn->query("SELECT provider_name, provider_mobile, password, email FROM providers WHERE provider_id='$provider_id'")->fetch_assoc();
    if(!$provider){
        echo 'Provider not found';
        exit;
    }

    // Generate new sicker_id
    $last = $conn->query("SELECT sicker_id FROM sickers ORDER BY sicker_id DESC LIMIT 1")->fetch_assoc();
    $new_id = $last ? $last['sicker_id'] + 1 : 1201;

    // Default values for missing columns
    $dob = null;
    $gender = null;
    $age = null;
    $city = null;
    $area = null;
    $request_date = date('Y-m-d'); // current date
    $image = null;
    $aadhar_doc = null;
    $sstatus = 'pending'; // new seeker status
    $email = $provider['email'] ?? null;

    // Insert into sickers
    $stmt = $conn->prepare("INSERT INTO sickers (sicker_id, sicker_name, dob, sicker_gender, sicker_age, sicker_mobile, city, area, request_date, password, image, aadhar_doc, status, email) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param(
        "isssisssssssss",
        $new_id,
        $provider['provider_name'],
        $dob,
        $gender,
        $age,
        $provider['provider_mobile'],
        $city,
        $area,
        $request_date,
        $provider['password'],
        $image,
        $aadhar_doc,
        $sstatus,
        $email
    );

    if(!$stmt->execute()){
        echo 'Failed to convert';
        exit;
    }

    // Update provider status
    $conn->query("UPDATE providers SET status='converted' WHERE provider_id='$provider_id'");

    echo 'success';
    exit;

} else {
    // Just update provider status
    $stmt = $conn->prepare("UPDATE providers SET status=? WHERE provider_id=?");
    $stmt->bind_param("si", $status, $provider_id);
    if($stmt->execute()){
        echo 'success';
    } else {
        echo 'Failed to update';
    }
}
?>
