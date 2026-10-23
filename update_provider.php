<?php
include 'db.php';

$provider = [];
$showForm = false;

// Fetch all cities
$cities = $conn->query("SELECT * FROM cities ORDER BY city_name ASC")->fetch_all(MYSQLI_ASSOC);

// Search Provider
if (isset($_POST['search'])) {
    $provider_id = $_POST['provider_id'];
    $stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id=?");
    $stmt->bind_param("s", $provider_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) { 
        $provider = $result->fetch_assoc();
        $showForm = true;
    } else {
        echo "<script>alert('Provider not found!');</script>";
    }
}

// Update Provider
if (isset($_POST['update'])) {
    $id = $_POST['provider_id'];
    $name = $_POST['provider_name'];
    $dob = $_POST['provider_dob'];
$from = new DateTime($dob);
$to = new DateTime('today');
$age = $from->diff($to)->y;

    $gender = $_POST['provider_gender'];
    $mobile = $_POST['provider_mobile'];
    $email = $_POST['email'];
    $city = $_POST['city'];
    $area = $_POST['area'];
    $date = $_POST['date'];
    $password = $_POST['password'];

    if(!is_dir("uploads")) mkdir("uploads", 0777, true);

    // Handle Profile Image
    $image_name = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $image_name = "uploads/img_" . time() . "_" . bin2hex(random_bytes(5)) . "." . $ext;
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_name);
    }
  
    // Handle Aadhar
    $aadhar_doc = $_POST['existing_aadhar'] ?? '';
    if (isset($_FILES['aadhar_doc']) && $_FILES['aadhar_doc']['name'] != "") {
        $ext = pathinfo($_FILES["aadhar_doc"]["name"], PATHINFO_EXTENSION);
        $aadhar_doc = "uploads/aadhar_" . time() . "_" . bin2hex(random_bytes(5)) . "." . $ext;
        move_uploaded_file($_FILES["aadhar_doc"]["tmp_name"], $aadhar_doc);
    }

 $stmt = $conn->prepare("UPDATE providers SET provider_name=?, email=?, provider_dob=?, provider_gender=?, provider_mobile=?, city=?, area=?, date=?, password=?, image=?, aadhar_doc=? WHERE provider_id=?");
$stmt->bind_param("ssssssssssss", $name, $email, $dob, $gender, $mobile, $city, $area, $date, $password, $image_name, $aadhar_doc, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Provider updated successfully!'); window.location='update_provider.php';</script>";
    } else {
        echo "<script>alert('Error: ".$conn->error."');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Provider</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<style>
.img-box { width:100px; height:100px; object-fit:cover; border:1px solid #ccc; margin-bottom:5px; border-radius:6px; }
.img-large { width:120px; height:120px; object-fit:cover; border:2px solid #666; border-radius:50%; box-shadow:0px 0px 8px rgba(0,0,0,0.3); }
</style>
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:600px;">
<h2 class="text-center mb-4">Search & Update Provider</h2>

<!-- Search Form -->
<form method="POST" class="card p-4 shadow mb-3">
    <div class="mb-3">
        <label>Provider ID</label>
        <input type="text" name="provider_id" class="form-control" placeholder="e.g., P001" required>
    </div>
    <button type="submit" name="search" class="btn btn-primary w-100">Search</button>
</form>

<?php if($showForm): ?>
<form method="POST" enctype="multipart/form-data" class="card p-4 shadow">
    <input type="hidden" name="provider_id" value="<?= $provider['provider_id'] ?>">
    <input type="hidden" name="existing_image" value="<?= $provider['image'] ?>">
    <input type="hidden" name="existing_aadhar" value="<?= $provider['aadhar_doc'] ?>">

    <!-- Profile Image at Top -->
    <div class="text-center mb-4">
        <?php 
        $imgPath = (!empty($provider['image']) && file_exists($provider['image'])) ? $provider['image'] : "uploads/default.png"; 
        ?>
        <img id="preview" src="<?= $imgPath ?>" class="img-large"><br>
        Change Profile Picture:
        <input type="file" name="image" accept="image/*" class="form-control form-control-sm mt-2">
    </div>

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="provider_name" class="form-control" value="<?= htmlspecialchars($provider['provider_name']) ?>" required>
    </div>

    <div class="mb-3">
    <label>Date of Birth</label>
    <input type="date" name="provider_dob" id="dob" class="form-control" value="<?= $provider['provider_dob'] ?>" required>
</div>

<div class="mb-3">
    <label> Age</label>
    <input type="text" id="calculated_age" class="form-control" readonly>
</div>


    <div class="mb-3">
        <label>Gender</label><br>
        <input type="radio" name="provider_gender" value="Male" <?= $provider['provider_gender']=='Male'?'checked':'' ?>> Male
        <input type="radio" name="provider_gender" value="Female" <?= $provider['provider_gender']=='Female'?'checked':'' ?>> Female
        </div>

    <div class="mb-3">
        <label>Mobile</label>
        <input type="text" name="provider_mobile" class="form-control" value="<?= $provider['provider_mobile'] ?>" required>
    </div>
<div class="mb-3">
    <label>Email</label>
    <input type="email" name="provider_email" class="form-control" value="<?= htmlspecialchars($provider['email']) ?>" required>
</div>

    <!-- City -->
    <div class="mb-3">
        <label>City</label>
        <select name="city" id="city" class="form-control" required>
            <option value="">--Select City--</option>
            <?php foreach($cities as $c): ?>
                <option value="<?= htmlspecialchars($c['city_name']) ?>" <?= $provider['city']==$c['city_name']?'selected':'' ?>><?= htmlspecialchars($c['city_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Area -->
    <div class="mb-3">
        <label>Area</label>
        <select name="area" id="area" class="form-control" required>
            <option value="<?= htmlspecialchars($provider['area']) ?>"><?= htmlspecialchars($provider['area']) ?></option>
        </select>
    </div>

    <div class="mb-3">
        <label> Registration Date</label>
        <input type="date" name="date" class="form-control" value="<?= $provider['date'] ?>" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="text" name="password" class="form-control" value="<?= $provider['password'] ?>" required>
    </div>

    <!-- Aadhar -->
    <div class="mb-3">
        <label>Aadhar Document</label><br>
        <?php 
        $aPath = (!empty($provider['aadhar_doc']) && file_exists($provider['aadhar_doc'])) ? $provider['aadhar_doc'] : '';
        if($aPath):
            $ext = strtolower(pathinfo($aPath, PATHINFO_EXTENSION));
            ?>
            <button type="button" class="btn btn-sm btn-secondary mb-2" onclick="$('#aadharContainer').toggle()">Show/Hide Document</button>
            <div id="aadharContainer" style="display:none;">
                <?php if(in_array($ext, ['jpg','jpeg','png'])): ?>
                    <img src="<?= $aPath ?>" class="img-box" id="aadharPreview">
                <?php else: ?>
                    <p><a href="<?= $aPath ?>" target="_blank">View Existing PDF</a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <input type="file" name="aadhar_doc" accept=".pdf" class="form-control" id="aadharInput">
    </div>

    <button type="submit" name="update" class="btn btn-success w-100">Update Provider</button>
</form>
<?php endif; ?>
</div>

<script>
// Profile image preview
$('input[name="image"]').on('change', function(e){
    const [file] = e.target.files;
    if(file){
        $('#preview').attr('src', URL.createObjectURL(file));
    } else {
        $('#preview').attr('src', 'uploads/default.png');
    }
});

// Aadhar preview update if image
$('#aadharInput').on('change', function(e){
    const [file] = e.target.files;
    if(file){
        const ext = file.name.split('.').pop().toLowerCase();
        if(['jpg','jpeg','png'].includes(ext)){
            if($('#aadharPreview').length){
                $('#aadharPreview').attr('src', URL.createObjectURL(file)).show();
            } else {
                $('#aadharContainer').html('<img src="'+URL.createObjectURL(file)+'" class="img-box" id="aadharPreview">').show();
            }
        }
    }
});

// Load areas dynamically
$('#city').on('change', function(){
    var city = $(this).val();
    if(city){
        $.post('get_area1.php', {city:city}, function(data){
            $('#area').html(data);
        });
    } else {
        $('#area').html('<option value="">--Select Area--</option>');
    }
});




// Auto-calculate age on DOB change
$(document).ready(function(){
    function calculateAge(dob) {
        const birthDate = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }
 
    // When DOB is selected, update age
    $('#dob').on('change', function(){
        const dob = $(this).val();
        if (dob) {
            const age = calculateAge(dob);
            $('#calculated_age').val(age + ' years');
        } else {
            $('#calculated_age').val('');
        }
    });

    // Trigger age calc on page load if DOB is already filled
    if ($('#dob').val()) {
        $('#dob').trigger('change');
    }
});

</script>
</body>
</html>
