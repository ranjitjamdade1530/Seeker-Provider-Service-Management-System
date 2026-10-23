<?php
include 'db.php';
header('Content-Type: text/plain');

$id = $_POST['id'] ?? '';
$type = $_POST['type'] ?? '';
$status = $_POST['status'] ?? '';

if ($id === '' || $type === '' || $status === '') {
    echo 'Missing parameters';
    exit;
}

// validate type & status
if (!in_array($type,['sicker','provider']) || !in_array($status,['pending','active','inactive','converted'])){
    echo 'Invalid input';
    exit;
}

// choose table and ID column
$table = $type==='sicker' ? 'sickers' : 'providers';
$id_col = $type==='sicker' ? 'sicker_id' : 'provider_id';

// Update only the selected row
$stmt = $conn->prepare("UPDATE `$table` SET status=? WHERE `$id_col`=? LIMIT 1");
$stmt->bind_param('ss', $status, $id); // **both are strings**
$stmt->execute();

if($stmt->affected_rows > 0){
    echo 'success';
}else{
    echo 'No row updated';
}
 
$stmt->close();
$conn->close();
?>
