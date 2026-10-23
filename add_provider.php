<?php
include 'db.php';

// Generate next Provider ID
$result = $conn->query("SELECT provider_id FROM providers ORDER BY provider_id DESC LIMIT 1");
$nextId = ($result->num_rows > 0) ? 'P' . str_pad(intval(substr($result->fetch_assoc()['provider_id'], 1)) + 1, 3, '0', STR_PAD_LEFT) : 'P001';

// Handle form submission
if (isset($_POST['save'])) {
    $provider_id = $_POST['provider_id'];
    $name = $_POST['provider_name'];
    $dob = $_POST['provider_dob'];

// Calculate age from DOB
$from = new DateTime($dob);
$to   = new DateTime('today'); 
$age = $from->diff($to)->y;
    $gender = $_POST['provider_gender'];
    $mobile = $_POST['provider_mobile'];
    $email = $_POST['provider_email'];
    $city = $_POST['city'];
    $area = $_POST['area'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // --- Image Upload ---
        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $img_name = 'img_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $img_path = 'uploads/' . $img_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $img_path)) {
                $image = $img_path;
            }
        }

        // --- Aadhar Document Upload ---
        $aadhar_doc = '';
        if (!empty($_FILES['aadhar_doc']['name'])) {
            $ext = pathinfo($_FILES['aadhar_doc']['name'], PATHINFO_EXTENSION);
            $doc_name = 'aadhar_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $doc_path = 'uploads/' . $doc_name;
            if (move_uploaded_file($_FILES['aadhar_doc']['tmp_name'], $doc_path)) {
                $aadhar_doc = $doc_path;
            }
        }

        // Insert into database
       $sql = "INSERT INTO providers 
(provider_id, provider_name,  provider_dob, provider_gender, provider_mobile, city, area, date, password, image, aadhar_doc,email)
VALUES 
('$provider_id','$name','$dob','$gender','$mobile','$city','$area',CURDATE(),'$password','$image','$aadhar_doc','$email')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Provider added successfully!'); window.location='';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>

<script>
    // Auto calculate and display age
document.addEventListener("DOMContentLoaded", function () {
    const dobInput = document.getElementById("dob");
    const ageInput = document.getElementById("age");

    dobInput.addEventListener("change", function () {
        const dob = new Date(this.value);
        if (!isNaN(dob)) {
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            ageInput.value = age + " years"; // ✅ display in textbox
        } else {
            ageInput.value = "";
        }
    });
});


</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Provider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background: #f8f9fa; }
        .card { max-width: 750px; margin: 50px auto; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        h2 { color: #007bff; text-align: center; margin-bottom: 30px; }
        #preview { width:100px; height:100px; object-fit:cover; border:1px solid #ccc; margin-bottom:5px; box-shadow:2px 2px 6px rgba(0,0,0,0.3); border-radius:6px; }
    </style>
</head>
<body> 

<div class="card">
    <h2>Add New Provider</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3 d-flex align-items-start">
            <div style="flex:1;">
                <label>Provider ID</label>
                <input type="text" name="provider_id" class="form-control" value="<?= $nextId ?>" readonly>
            </div>
            <div style="margin-left:15px; text-align:center;">
                <img id="preview" src="uploads/default.png" alt="Profile Preview"><br>
                Profile Picture:
                <input type="file" name="image" class="form-control form-control-sm" style="width:70px; padding:2px;" accept="image/*">
            </div>
        </div> 

        <div class="mb-3">
            <label>Provider Name</label>
            <input type="text" name="provider_name" class="form-control" required>
        </div>

       <div class="mb-3">
    <label>Date of Birth</label>
    <input type="date" name="provider_dob" id="dob" class="form-control" required>
</div>

<div class="mb-3">
    <label>Age</label>
    <input type="text" name="provider_age" id="age" class="form-control" readonly>
</div>

        <div class="mb-3">
            <label>Gender</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" name="provider_gender" value="Male" class="form-check-input" required> Male
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" name="provider_gender" value="Female" class="form-check-input" required> Female
            </div>
        </div>

        <div class="mb-3">
            <label>Mobile No</label>
            <input type="text" name="provider_mobile" class="form-control" maxlength="10" pattern="[0-9]{10}" required>
        </div>
<div class="mb-3">
    <label>Email</label>
    <input type="email" name="provider_email" class="form-control" required>
</div>

        <div class="mb-3">
            <label>City</label>
            <select name="city" id="city" class="form-select" required>
                <option value="">Select City</option>
                <?php
                $cities = $conn->query("SELECT DISTINCT city_name FROM areas ORDER BY city_name");
                while ($row = $cities->fetch_assoc()) {
                    echo "<option value='" . $row['city_name'] . "'>" . $row['city_name'] . "</option>";
                }
                ?>
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
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Aadhar Document</label>
            <input type="file" name="aadhar_doc" class="form-control" accept=".pdf">
        </div>

        <button type="submit" name="save" class="btn btn-primary w-100">Save Provider</button>
    </form>
</div>

<script>
// Load areas based on selected city
$('#city').on('change', function() {
    const city_name = $(this).val();
    $('#area').html('<option>Loading...</option>');
    $.post('get_areas.php', { city_name: city_name }, function(response) {
        $('#area').html(response);
    });
});
 
// Image preview
$('input[name="image"]').on('change', function(e){
    const [file] = e.target.files;
    if(file){
        $('#preview').attr('src', URL.createObjectURL(file));
    } else {
        $('#preview').attr('src', 'uploads/default.png');
    }
});
</script>
</body>
</html>
