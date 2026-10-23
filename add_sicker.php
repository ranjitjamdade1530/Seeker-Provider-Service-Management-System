<?php
include 'db.php';

// Fetch city data
$cities = $conn->query("SELECT * FROM cities");

// Function to generate next Sicker ID
function generateSickerID($conn) {
    $result = $conn->query("SELECT sicker_id FROM sickers ORDER BY sicker_id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastId = $row['sicker_id'];   // Example: "S005"
        $num = intval(substr($lastId, 1)); // Remove "S" → 5
        $nextNum = $num + 1;
        return "S" . str_pad($nextNum, 3, "0", STR_PAD_LEFT); // → "S006"
    } else {
        return "S001"; // First record
    }
}

$new_sicker_id = generateSickerID($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sicker_id = $_POST['sicker_id']; // Use generated ID
    $sicker_name = $_POST['sicker_name'];
    $dob = $_POST['dob'];
    $sicker_age = date_diff(date_create($dob), date_create('today'))->y;
    $sicker_gender = $_POST['sicker_gender'];
    $sicker_mobile = $_POST['sicker_mobile'];
    
$email = $_POST['email'];
    $city = $_POST['city'];
    $area = $_POST['area'];
    $password = $_POST['password']; 

    if ($sicker_age < 60) {
        echo "<script>alert('Sicker must be 60 years or older!'); window.location='add_sicker.php';</script>";
        exit;
    }

    // Upload image
    $image = $_FILES['image']['name'];
    $target_image = "uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_image);

    // Upload Aadhar
    $aadhar_doc = $_FILES['aadhar_doc']['name'];
    $target_aadhar = "uploads/" . basename($aadhar_doc);
    move_uploaded_file($_FILES['aadhar_doc']['tmp_name'], $target_aadhar);

    // Insert with custom ID
   $sql = "INSERT INTO sickers 
    (sicker_id, sicker_name, dob, sicker_age, sicker_gender, sicker_mobile, city, area, request_date, password, image, aadhar_doc, email)
    VALUES 
    ('$sicker_id', '$sicker_name',  '$dob', '$sicker_age', '$sicker_gender', '$sicker_mobile', '$city', '$area', CURDATE(), '$password', '$image', '$aadhar_doc','$email')";

    if ($conn->query($sql)) {
        echo "<script>alert('Sicker added successfully with ID: $sicker_id'); window.location='add_sicker.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html> 
<html>
<head>
    <title>Add Sicker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width: 550px;">
    <h3>Add New Seeker</h3>
    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

        <div class="mb-3 d-flex align-items-start">
            <div style="flex:1;">
                <label>Seeker ID</label>
                <input type="text" name="sicker_id" id="sicker_id" class="form-control" value="<?= $new_sicker_id ?>" readonly>
            </div>
            <div style="margin-left:15px; text-align:center;">
                <img id="preview" src="uploads/default.png" alt="Image Preview"
                      style="width:100px; height:100px; object-fit:cover; border:1px solid #ccc;
                            display:block; margin-bottom:5px; box-shadow:2px 2px 6px rgba(0,0,0,0.3); border-radius:6px;">
               Profile Picture:
               <input type="file" name="image" class="form-control form-control-sm" accept="image/*" style="width:95px; padding:2px;">
            </div>
        </div>

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="sicker_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob" id="dob" class="form-control" required onchange="calculateAge()">
        </div>

        <div class="mb-3">
            <label>Age</label>
            <input type="text" name="sicker_age" id="sicker_age" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label>Gender</label><br>
            <input type="radio" name="sicker_gender" value="Male" required> Male
            <input type="radio" name="sicker_gender" value="Female" required> Female
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text" name="sicker_mobile" pattern="[0-9]{10}" maxlength="10" class="form-control" required>
        </div>
<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
</div>

        <div class="mb-3">
            <label>City</label>
            <select name="city" id="city" class="form-select" onchange="loadAreas(this.value)" required>
                <option value="">Select City</option>
                <?php while($row = $cities->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['city_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Area</label>
            <select name="area" id="area" class="form-select" required>
                <option value="">Select Area</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" id="confirm_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Upload Aadhar Document</label>
            <input type="file" name="aadhar_doc" class="form-control" accept=".pdf">
        </div>

        <center><button type="submit" class="btn btn-primary">Save</button></center>
    </form>
</div>

<script>
function validateForm() {
    let pass = document.getElementById("password").value;
    let cpass = document.getElementById("confirm_password").value;
    let age = parseInt(document.getElementById("sicker_age").value);

    if (pass !== cpass) {
        alert("Passwords do not match!");
        return false;
    }
    if (age < 60) {
        alert("Sicker must be 60 years or older!");
        return false;
    }
    return true;
}

function loadAreas(cityId) {
    fetch("load_areas.php?city_id=" + cityId)
        .then(res => res.text())
        .then(data => {
            document.getElementById("area").innerHTML = data;
        });
}

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
        document.getElementById("sicker_age").value = age +"years";
    }
}

document.querySelector('input[name="image"]').addEventListener('change', function(e){
    const [file] = e.target.files;
    if(file){
        document.getElementById('preview').src = URL.createObjectURL(file);
    } else { 
        document.getElementById('preview').src = 'uploads/default.png';
    }
});
</script>
</body>
</html>
