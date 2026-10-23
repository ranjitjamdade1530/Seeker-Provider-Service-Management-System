<?php
include 'db.php';

// Debug log to see inputs
file_put_contents('debug.txt', print_r($_POST, true));

function generateSickerId($conn) {
    $res = $conn->query("SELECT sicker_id FROM sickers ORDER BY id DESC LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $num = (int)substr($row['sicker_id'], 1); // remove 'S'
        return 'S' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'S001';
}

if (isset($_POST['id'], $_POST['status'], $_POST['type'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);
    $type = $_POST['type'];

    if ($type === 'provider') {
        if ($status === 'converted') {
            // Step 1: Get provider
            $res = $conn->query("SELECT * FROM providers WHERE provider_id = '$id'");
            if ($res && $res->num_rows > 0) {
                $provider = $res->fetch_assoc();

                // Step 2: Generate sicker_id
                $sicker_id = generateSickerId($conn);

                // Step 3: Calculate age
                $dob = $provider['provider_dob'];
                $age = '';
                if ($dob && strtotime($dob)) {
                    $birthDate = new DateTime($dob);
                    $today = new DateTime();
                    $age = $today->diff($birthDate)->y;
                }

                // Step 4: Insert into sickers
                $stmt = $conn->prepare("INSERT INTO sickers (
                    sicker_id, sicker_name, dob, sicker_age, sicker_gender,
                    sicker_mobile, city, area, request_date, password,
                    image, aadhar_doc, status, email
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $request_date = date('Y-m-d');

                $stmt->bind_param(
                    "ssssssssssssss",
                    $sicker_id,
                    $provider['provider_name'],
                    $provider['provider_dob'],
                    $age,
                    $provider['provider_gender'],
                    $provider['provider_mobile'],
                    $provider['city'],
                    $provider['area'],
                    $request_date,
                    $provider['password'],
                    $provider['image'],
                    $provider['aadhar_doc'],
                    $status,
                    $provider['email']
                );

                if ($stmt->execute()) {
                    // Step 5: Update status
                    $conn->query("UPDATE providers SET status = 'converted' WHERE provider_id = '$id'");
                    echo 'success';
                } else {
                    echo 'Insert error: ' . $stmt->error;
                }
            } else {
                echo 'Provider not found';
            }
        } else {
            // Normal status update
            if ($conn->query("UPDATE providers SET status = '$status' WHERE provider_id = '$id'")) {
                echo 'success';
            } else {
                echo 'Update error: ' . $conn->error;
            }
        }
    } else {
        echo 'Invalid type';
    }
} else {
    echo 'Missing data';
}
?>
