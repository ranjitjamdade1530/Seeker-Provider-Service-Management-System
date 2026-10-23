<?php
include 'db.php';
$showForm = false;
$sicker = [];

// Fetch cities
$cities = $conn->query("SELECT * FROM cities ORDER BY city_name ASC")->fetch_all(MYSQLI_ASSOC);

// Search
if (isset($_POST['search'])) {
    $sicker_id = $_POST['sicker_id'];
    $stmt = $conn->prepare("SELECT * FROM sickers WHERE sicker_id = ?");
    $stmt->bind_param("s", $sicker_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $sicker = $result->fetch_assoc();
        $showForm = true;
    } else {
        echo "<script>alert('Sicker ID not found!');</script>";
    }
}  

// Update
if (isset($_POST['update'])) {
    $sicker_id = $_POST['sicker_id'];
    $sicker_name = $_POST['sicker_name'];
    $dob = $_POST['dob'];
    $sicker_age = date_diff(date_create($dob), date_create('today'))->y;
    $sicker_gender = $_POST['sicker_gender'];
    $sicker_mobile = $_POST['sicker_mobile'];
    $email = $_POST['email'];
    $city = $_POST['city'];
    $area = $_POST['area'];
    $request_date = $_POST['request_date'];
    $password = $_POST['password'];

    if($sicker_age < 60){
        echo "<script>alert('Sicker must be 60 years or older!'); window.history.back();</script>";
        exit;
    }

    // Profile image
    $image_name = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        if(!is_dir("uploads")) mkdir("uploads");
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $image_name);
    }

    // Aadhar document
    $aadhar_doc = $_POST['existing_aadhar'] ?? '';
    if (isset($_FILES['aadhar_doc']) && $_FILES['aadhar_doc']['name'] != "") {
        if(!is_dir("uploads")) mkdir("uploads");
        $aadhar_doc = time() . "_" . basename($_FILES["aadhar_doc"]["name"]);
        move_uploaded_file($_FILES["aadhar_doc"]["tmp_name"], "uploads/" . $aadhar_doc);
    }

    $stmt = $conn->prepare("UPDATE sickers 
    SET sicker_name=?, email=?, dob=?, sicker_age=?, sicker_gender=?, sicker_mobile=?, city=?, area=?, request_date=?, password=?, image=?, aadhar_doc=? 
    WHERE sicker_id=?");
$stmt->bind_param("ssssissssssss", $sicker_name, $email, $dob, $sicker_age, $sicker_gender, $sicker_mobile, $city, $area, $request_date, $password, $image_name, $aadhar_doc, $sicker_id);

    if ($stmt->execute()) {
        echo "<script>alert('Sicker details updated successfully!'); window.location='update_sicker.php';</script>";
        $showForm = false;
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Sicker</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<style>
.img-box { width: 120px; height: 120px; object-fit: cover; border:2px solid #ccc; margin-bottom:10px; box-shadow:2px 2px 8px rgba(0,0,0,0.3); border-radius:50%; }
.small-input { width: 70px; padding: 2px; }
</style>
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width: 700px;">
<h2 class="text-center mb-4">Update Seeker Details</h2>

<!-- Search Form -->
<form method="POST" class="mb-4 p-3 bg-white shadow rounded">
    <div class="row">
        <div class="col-md-8">
            <label class="form-label">Enter Seeker ID</label>
            <input type="text" name="sicker_id" class="form-control" placeholder="e.g., S001" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" name="search" class="btn btn-primary w-100">Search</button>
        </div>
    </div>
</form>
 
<?php if ($showForm): ?>
<form method="POST" enctype="multipart/form-data" class="p-4 bg-white shadow rounded" onsubmit="return validateForm()">
    <input type="hidden" name="sicker_id" value="<?= $sicker['sicker_id'] ?>">
    <input type="hidden" name="existing_image" value="<?= $sicker['image'] ?>">
    <input type="hidden" name="existing_aadhar" value="<?= $sicker['aadhar_doc'] ?>">

    <!-- Profile Image always at top -->
    <div class="text-center mb-3">
        <img id="preview" src="<?= $sicker['image']? 'uploads/'.$sicker['image'] : 'uploads/default.png' ?>" class="img-box">
        <div class="mt-2">
            Change Profile Picture:
            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Seeker Name</label>
        <input type="text" name="sicker_name" class="form-control" value="<?= htmlspecialchars($sicker['sicker_name']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="dob" id="dob" class="form-control" value="<?= $sicker['dob'] ?>" required onchange="calculateAge()">
    </div>

    <div class="mb-3">
        <label class="form-label">Age</label>
        <input type="text" name="sicker_age" id="sicker_age" class="form-control" value="<?= $sicker['sicker_age'] ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Gender</label><br>
        <input type="radio" name="sicker_gender" value="Male" <?= $sicker['sicker_gender']=='Male'?'checked':'' ?>> Male
        <input type="radio" name="sicker_gender" value="Female" <?= $sicker['sicker_gender']=='Female'?'checked':'' ?>> Female
          </div>

    <div class="mb-3">
        <label class="form-label">Mobile</label>
        <input type="text" name="sicker_mobile" class="form-control" value="<?= $sicker['sicker_mobile'] ?>" required>
    </div>
<div class="mb-3"> 
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($sicker['email']) ?>" required>
</div>

    <!-- City & Area -->
    <div class="mb-3">
        <label class="form-label">City</label>
        <select name="city" id="city" class="form-control" required>
            <option value="">--Select City--</option>
            <?php foreach($cities as $c): ?>
                <option value="<?= htmlspecialchars($c['city_name']) ?>" <?= $sicker['city']==$c['city_name']?'selected':'' ?>><?= htmlspecialchars($c['city_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Area</label>
        <select name="area" id="area" class="form-control" required>
            <option value="<?= htmlspecialchars($sicker['area']) ?>"><?= htmlspecialchars($sicker['area']) ?></option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Rgistration Dat</label>
        <input type="date" name="request_date" class="form-control" value="<?= $sicker['request_date'] ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="text" name="password" class="form-control" value="<?= $sicker['password'] ?>" required>
    </div>

    <!-- Aadhar Document -->
    <div class="mb-3">
        <label>Aadhar Document</label><br>
        <?php 
        $aPath = (!empty($sicker['aadhar_doc']) && file_exists("uploads/".$sicker['aadhar_doc'])) ? "uploads/".$sicker['aadhar_doc'] : '';
        if($aPath): ?>
            <button type="button" class="btn btn-sm btn-info mb-2" onclick="$('#oldDoc').toggle();">View Old Document</button>
            <div id="oldDoc" style="display:none;">
                <?php 
                $ext = pathinfo($aPath, PATHINFO_EXTENSION);
                if(in_array(strtolower($ext), ['jpg','jpeg','png'])): ?>
                    <img src="<?= $aPath ?>" class="img-box mb-2" id="aadharPreview">
                <?php else: ?>
                    <p><a href="<?= $aPath ?>" target="_blank">View Existing PDF</a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <input type="file" name="aadhar_doc" class="form-control form-control-sm" accept=".pdf">
    </div>

    <button type="submit" name="update" class="btn btn-success w-100">Update</button>
</form>
<?php endif; ?>
</div>

<script>
// Age calculation
function calculateAge() {
    let dob = document.getElementById("dob").value;
    if (dob) {
        let birthDate = new Date(dob);
        let today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        let m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        document.getElementById("sicker_age").value = age + " years";
    }
} 

function validateForm() {
    let age = parseInt(document.getElementById("sicker_age").value);
    if(age < 60){ alert("Sicker must be 60 years or older!"); return false; }
    return true;
}

// Preview profile image
$('input[name="image"]').on('change', function(e){
    const [file] = e.target.files;
    if(file) $('#preview').attr('src', URL.createObjectURL(file));
});

// Preview aadhar if image
$('input[name="aadhar_doc"]').on('change', function(e){
    const [file] = e.target.files;
    if(file){
        const ext = file.name.split('.').pop().toLowerCase();
        if(['jpg','jpeg','png'].includes(ext)) $('#aadharPreview').attr('src', URL.createObjectURL(file)).show();
    }
});

// Load areas based on city
$('#city').on('change', function(){
    var city = $(this).val();
    if(city){
        $.ajax({
            url:'get_area1.php',
            type:'POST',
            data:{city:city},
            success:function(data){ $('#area').html(data); }
        });
    } else { $('#area').html('<option value="">--Select Area--</option>'); }
});
</script>
</body>
</html>
