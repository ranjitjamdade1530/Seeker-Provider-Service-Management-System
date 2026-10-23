<?php
session_start();
require 'db.php';

header('X-Content-Type-Options: nosniff'); // minor header

if (!isset($_SESSION['sicker_id'])) {
    // If AJAX request, respond JSON
    if (!empty($_POST['ajax'])) {
        echo json_encode(['success'=>false,'message'=>'Session expired. Please login again.']);
        exit;
    } else {
        echo "<div class='alert alert-danger'>Session expired. Please login again.</div>";
        exit();
    }
} 

$sicker_id = $_SESSION['sicker_id'];

// Fetch cities
$cities = $conn->query("SELECT * FROM cities ORDER BY city_name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch sicker details
$stmt = $conn->prepare("SELECT * FROM sickers WHERE sicker_id=?");
$stmt->bind_param("s", $sicker_id);
$stmt->execute();
$sicker = $stmt->get_result()->fetch_assoc();

// Handle update (both normal POST and AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $name = $_POST['sicker_name'] ?? '';
    $age = $_POST['sicker_age'] ?? '';
    $gender = $_POST['sicker_gender'] ?? '';
    $mobile = $_POST['sicker_mobile'] ?? '';
    $email = $_POST['sicker_email'] ?? '';
    $city = $_POST['city'] ?? '';
    $area = $_POST['area'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!is_dir("uploads")) mkdir("uploads", 0777, true);

    // Profile Image
    $image = $_POST['existing_image'] ?? $sicker['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = "uploads/sicker_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
            // If ajax, return JSON, else echo
            $msg = 'Image upload failed.';
            if (!empty($_POST['ajax'])) {
                echo json_encode(['success'=>false,'message'=>$msg]); exit;
            } else {
                echo "<div class='alert alert-warning'>$msg</div>";
            }
        }
    }

// Aadhaar Document
$aadhar_doc = $_POST['existing_aadhar'] ?? $sicker['aadhar_doc'];
if (!empty($_FILES['aadhar_doc']['name'])) {
    $ext = pathinfo($_FILES['aadhar_doc']['name'], PATHINFO_EXTENSION);
    $aadhar_doc = "uploads/aadhar_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
    if (!move_uploaded_file($_FILES['aadhar_doc']['tmp_name'], $aadhar_doc)) {
        $msg = 'Aadhaar upload failed.';
        if (!empty($_POST['ajax'])) {
            echo json_encode(['success'=>false,'message'=>$msg]); exit;
        } else {
            echo "<div class='alert alert-warning'>$msg</div>";
        }
    }
}


  $stmt = $conn->prepare("UPDATE sickers 
    SET sicker_name=?, email=?, sicker_age=?, sicker_gender=?, sicker_mobile=?, city=?, area=?, password=?, image=?, aadhar_doc=? 
    WHERE sicker_id=?");


    if (!$stmt) {
        $err = "Prepare failed: " . $conn->error;
        if (!empty($_POST['ajax'])) { echo json_encode(['success'=>false,'message'=>$err]); exit; }
        else { die("<div class='alert alert-danger'>$err</div>"); }
    }

  $stmt->bind_param(
    "ssssissssss", 
    $name, $email, $age, $gender, $mobile, $city, $area, $password, $image, $aadhar_doc, $sicker_id
);
    if ($stmt->execute()) {
        $_SESSION['sicker_name'] = $name;
        if (!empty($_POST['ajax'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success'=>true,'message'=>'Profile updated successfully!','name'=>$name]);
            exit;
        } else {
            echo "<script>alert('Profile updated successfully!'); location.reload();</script>";
            exit;
        } 
    } else {
        $err = "Update failed: " . $stmt->error;
        if (!empty($_POST['ajax'])) { echo json_encode(['success'=>false,'message'=>$err]); exit; }
        else { echo "<div class='alert alert-danger'>$err</div>"; }
    }
}
?>

<!-- PROFILE HTML (this will be loaded into #mainContent via AJAX) -->
<div class="card p-4 shadow">
    <h4 class="mb-3">Update Profile</h4>

    <!-- profile form id used by dashboard.js AJAX handler -->
    <form id="profileForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($sicker['image']) ?>">
        <input type="hidden" name="update" value="1">

        <!-- Profile Image -->
        <div class="mb-3 text-center">
            <?php 
            $img = (!empty($sicker['image']) && file_exists($sicker['image'])) ? $sicker['image'] : "uploads/default.png";
            ?>
            <img src="<?= $img ?>" id="preview" class="rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover;border:2px solid #ccc;">
            <input type="file" name="image" class="form-control form-control-sm mt-2" accept="image/*">
        </div>
<div class="mb-3">
    <label>Sicker ID</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($sicker['sicker_id']) ?>" readonly>
</div>

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="sicker_name" class="form-control" value="<?= htmlspecialchars($sicker['sicker_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Age</label>
            <input type="number" name="sicker_age" class="form-control" value="<?= htmlspecialchars($sicker['sicker_age']) ?>" required readonly>
        </div>

        <div class="mb-3">
            <label>Gender</label><br>
            <input type="radio" name="sicker_gender" value="Male" <?= $sicker['sicker_gender']=='Male'?'checked':'' ?>> Male
            <input type="radio" name="sicker_gender" value="Female" <?= $sicker['sicker_gender']=='Female'?'checked':'' ?>> Female
             </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text" name="sicker_mobile" class="form-control" value="<?= htmlspecialchars($sicker['sicker_mobile']) ?>" required>
        </div>
<div class="mb-3">
    <label>Email</label>
    <input type="email" name="sicker_email" class="form-control" value="<?= htmlspecialchars($sicker['email'] ?? '') ?>" required>
</div>

        <div class="mb-3">
            <label>City</label>
            <select name="city" id="city_profile" class="form-control" required>
                <option value="">--Select City--</option>
                <?php foreach($cities as $c): ?>
                    <option value="<?= htmlspecialchars($c['city_name']) ?>" <?= $sicker['city']==$c['city_name']?'selected':'' ?>><?= htmlspecialchars($c['city_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Area</label>
            <select name="area" id="area_profile" class="form-control" required>
                <option value="<?= htmlspecialchars($sicker['area']) ?>"><?= htmlspecialchars($sicker['area']) ?></option>
            </select>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($sicker['password']) ?>" required>
        </div>

<div class="mb-3">
    <label>Aadhaar Document</label><br>

    <?php 
    $aadharPath = !empty($sicker['aadhar_doc']) ? 'uploads/' . basename($sicker['aadhar_doc']) : '';
    if (!empty($aadharPath) && file_exists($aadharPath)): ?>
        <a href="<?= htmlspecialchars($aadharPath) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-2">
            Show Aadhaar (PDF)
        </a><br>
    <?php elseif (!empty($aadharPath)): ?>
        <span class="text-warning">Aadhaar file not found on server</span><br>
    <?php else: ?>
        <span class="text-muted">No Aadhaar uploaded</span><br>
    <?php endif; ?>

    <!-- Keep existing file path so updating other fields doesn't remove it -->
    <input type="hidden" name="existing_aadhar" value="<?= htmlspecialchars($sicker['aadhar_doc']) ?>">

    <!-- Upload new Aadhaar -->
    <input type="file" name="aadhar_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
</div>






        <button type="submit" class="btn btn-success w-100">Update Profile</button>
    </form>
</div>

<!-- Inline scripts (also kept as fallback when profile.php is loaded directly) -->
<script>
// Live image preview (works also when loaded directly)
$('input[name="image"]').on('change', function(e){
    const [file] = e.target.files;
    if(file){
        $('#preview').attr('src', URL.createObjectURL(file));
    }
});

// Dynamic area loading (direct load fallback)
$('#city_profile').on('change', function(){
    var city = $(this).val();
    if(city){
        $.post('get_area1.php', {city:city}, function(data){
            $('#area_profile').html(data);
        });
    } else {
        $('#area_profile').html('<option value="">--Select Area--</option>');
    }
});
</script>
