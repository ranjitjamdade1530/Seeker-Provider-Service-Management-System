<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Failed to send request'];

if(isset($_POST['sicker_id'], $_POST['provider_id'], $_POST['request_type'], $_POST['slot'], $_POST['city'], $_POST['area'], $_POST['request_date'], $_POST['note'])){

    $sicker_id   = $_POST['sicker_id'];
    $provider_id = $_POST['provider_id'];
    $request_type = $_POST['request_type'];
    $slot        = $_POST['slot'];
    $city        = $_POST['city'];
    $area        = $_POST['area'];
    $request_date = $_POST['request_date']; 
    $note        = trim($_POST['note']);

    // ✅ Validation: note max 100 words
    $wordCount = str_word_count($note);
    if($wordCount > 100){
        $response['message'] = "Note cannot exceed 100 words (you used $wordCount).";
        echo json_encode($response);
        exit;
    }

    // ✅ Fetch sicker name & mobile
    $stmt_s = $conn->prepare("SELECT sicker_name, sicker_mobile FROM sickers WHERE sicker_id = ?");
    $stmt_s->bind_param("s", $sicker_id);
    $stmt_s->execute();
    $res_s = $stmt_s->get_result();
    if($res_s->num_rows === 0){
        $response['message'] = 'Invalid sicker';
        echo json_encode($response);
        exit;
    }
    $sicker = $res_s->fetch_assoc();
    $sicker_name = $sicker['sicker_name'];
    $sicker_mobile = $sicker['sicker_mobile'];

    // 🚫 Removed 10-hour limit check

    // ✅ Duplicate request check
    $stmt_check = $conn->prepare("SELECT request_id FROM requests 
        WHERE sicker_id=? AND provider_id=? AND request_type=? AND slot=? AND request_date=?");
    $stmt_check->bind_param("sssss", $sicker_id, $provider_id, $request_type, $slot, $request_date);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if($res_check->num_rows > 0){
        $response['message'] = 'Request already sent';
    } else {
        // ✅ Insert new request
        $stmt_ins = $conn->prepare("INSERT INTO requests 
            (sicker_id, user_name, user_mobile, provider_id, request_type, city, area, slot, status, request_date, note) 
            VALUES (?,?,?,?,?,?,?,?, 'Pending', ?, ?)");
        $stmt_ins->bind_param("ssssssssss", 
            $sicker_id, $sicker_name, $sicker_mobile, $provider_id, 
            $request_type, $city, $area, $slot, $request_date, $note
        );

        if($stmt_ins->execute()){
            $response['success'] = true;
            $response['message'] = '✅ Request sent successfully';
        } else {
            $response['message'] = 'Database error: '.$conn->error;
        }
    }
}else{
    $response['message'] = 'Invalid input';
}

echo json_encode($response);
?>
