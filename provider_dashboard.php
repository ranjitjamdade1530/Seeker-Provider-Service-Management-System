<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start();
require 'db.php';

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];
$provider_name = $_SESSION['provider_name'] ?? "Provider";

// Fetch provider profile
$stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id = ?");
$stmt->bind_param("s", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();
if (!$provider) { echo "Provider not found!"; exit(); }

// Handle profile update
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profile'])) {
    $name = $_POST['provider_name'] ?? '';
   
    $gender = $_POST['provider_gender'] ?? '';
    $mobile = $_POST['provider_mobile'] ?? '';
    $city = $_POST['city'] ?? '';
    $area = $_POST['area'] ?? '';
    $password = $_POST['password'] ?? '';
 
    if (!is_dir("uploads")) mkdir("uploads",0777,true);

    // profile image
    $image = $_POST['existing_image'] ?? $provider['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = "uploads/provider_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }
 
   $stmt = $conn->prepare("UPDATE providers 
        SET provider_name=?, provider_gender=?, provider_mobile=?, city=?, area=?, password=?, image=? 
        WHERE provider_id=?");
$stmt->bind_param("ssssssss", $name, $gender, $mobile, $city, $area, $password, $image, $provider_id);
if ($stmt->execute()) {
    $_SESSION['provider_name'] = $name;
    echo "<script>alert('Profile updated successfully!');window.location='provider_dashboard.php?tab=profile';</script>";
    exit;
} else {
    echo "<div class='alert alert-danger'>Update failed: " . $stmt->error . "</div>";
}
}
  
// Handle Add Request
if (isset($_POST['add_request'])) {
    $slot = $_POST['time_slot'] ?? '';
    $available_date = $_POST['available_date'] ?? '';
    $city = $_POST['city'] ?? '';
    $area = $_POST['area'] ?? '';
    $services = $_POST['services'] ?? [];

    if (!empty($slot) && !empty($available_date) && !empty($city) && !empty($area) && !empty($services)) {
        foreach ($services as $service) {
            $stmt = $conn->prepare("
                INSERT INTO provider_requests 
                (provider_id, time_slot, service_name, available_date, city, area) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("ssssss", $provider_id, $slot, $service, $available_date, $city, $area);

            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            } 
        }
        $success_msg = "Request saved successfully!";
    } 
}






// Handle profile update
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profile'])) {
    $name = $_POST['provider_name'] ?? '';
    $age = $_POST['provider_age'] ?? '';
    $gender = $_POST['provider_gender'] ?? '';
    $mobile = $_POST['provider_mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    $city = $_POST['city'] ?? '';
    $area = $_POST['area'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!is_dir("uploads")) mkdir("uploads",0777,true);
    if (!is_dir("uploads/aadhar")) mkdir("uploads/aadhar",0777,true);

    // profile image
    $image = $_POST['existing_image'] ?? $provider['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = "uploads/provider_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Aadhaar document
    $aadhar_doc = $_POST['existing_aadhar'] ?? $provider['aadhar_doc'];
    if (!empty($_FILES['aadhar_doc']['name'])) {
        $ext = pathinfo($_FILES['aadhar_doc']['name'], PATHINFO_EXTENSION);
        $aadhar_doc = "uploads/aadhar/aadhar_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
        move_uploaded_file($_FILES['aadhar_doc']['tmp_name'], $aadhar_doc);
    }

$stmt = $conn->prepare("UPDATE providers SET 
    provider_name=?, provider_gender=?, provider_mobile=?, email=?, city=?, area=?, password=?, image=?, aadhar_doc=? 
    WHERE provider_id=?");
   $stmt->bind_param(
    "ssssssssss",
    $provider_name,
    $provider_gender,
    $provider_mobile,
     $email,
    $city,
    $area,
    $password,
    $image_name,
    $aadhar_doc,
    $provider_id
);  if ($stmt->execute()) {
        $_SESSION['provider_name']=$name;
        echo "<script>alert('Profile updated successfully!');window.location='provider_dashboard.php?tab=profile';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Update failed: ".$stmt->error."</div>";
    }
}


// Fetch slots and services
$slots = $conn->query("SELECT time_range FROM time_slots ORDER BY time_range ASC")->fetch_all(MYSQLI_ASSOC);
$services_list = $conn->query("SELECT * FROM request_services ORDER BY service_name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch received requests
$req_stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id = ? ORDER BY request_date DESC");
$req_stmt->bind_param("s", $provider_id);
$req_stmt->execute();
$requests = $req_stmt->get_result();
?>
<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Provider Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body { background-color: #f8f9fa; }
.sidebar { height:100vh; background-color:#343a40; position:fixed; width:250px; padding-top:20px; }
.sidebar a { color:white; padding:12px 20px; display:block; text-decoration:none; }
.sidebar a:hover, .sidebar a.active { background-color:#0d6efd; }
.main-content { margin-left:250px; padding:20px; }
.navbar { margin-left:250px; }
</style>
</head>
<body>
 
<div class="sidebar">
    <h4 class="text-center text-white">Provider Panel</h4>
    <a href="?tab=dashboard" class="<?= (!isset($_GET['tab']) || $_GET['tab']=='dashboard')?'active':'' ?>"> <i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="?tab=profile" class="<?= (isset($_GET['tab']) && $_GET['tab']=='profile')?'active':'' ?>"> <i class="bi bi-person-circle"></i> Profile</a>
    <a href="?tab=add_request" class="<?= (isset($_GET['tab']) && $_GET['tab']=='add_request')?'active':'' ?>"> <i class="bi bi-plus-circle"></i> Add Request</a>
    <a href="?tab=received_requests" class="<?= (isset($_GET['tab']) && $_GET['tab']=='received_requests')?'active':'' ?>"> <i class="bi bi-inbox"></i> Received Requests</a>
   <a href="?tab=catalog" class="<?= (isset($_GET['tab']) && $_GET['tab']=='catalog')?'active':'' ?>"> <i class="bi bi-book"></i> Catalog</a>
<a href="?tab=emergency" class="<?= (isset($_GET['tab']) && $_GET['tab']=='emergency')?'active text-danger fw-bold':'' ?>">
    <i class="bi bi-exclamation-triangle-fill"></i> Emergency Conversion
</a>



    <a href="?tab=prodate"><i class="bi bi-calendar-date"></i> Seeker Request Date Report</a>
    <a href="?tab=promonth"><i class="bi bi-calendar-event"></i> Seeker Request Month Report</a>
    <a href="?tab=proyr"><i class="bi bi-calendar3"></i>  Seeker Request Year Report</a>
    <a href="?tab=age_report" class="<?= (isset($_GET['tab']) && $_GET['tab']=='age_report')?'active':'' ?>">
   <i class="bi bi-hourglass-split"></i> Provider as Seeker
</a>

<a href="?tab=report_requests" class="<?= (isset($_GET['tab']) && $_GET['tab']=='report_requests') ? 'active' : '' ?>">
    <i class="bi bi-file-earmark-text"></i> Send Request Report
</a>

    <a href="logout1.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <span class="navbar-brand">Welcome, <?= htmlspecialchars($provider_name) ?></span>
    </div>
</nav>

<div class="main-content">
<div class="container">
<?php
$tab = $_GET['tab'] ?? 'dashboard';

switch($tab) {
 
  
   case 'age_report':
    // Fetch provider DOB
    $dob = $provider['provider_dob'] ?? null;

    if(!$dob){
        echo "<div class='alert alert-warning'>DOB not found in your profile. Please update your profile.</div>";
    } else {
        $from = new DateTime($dob);
        $to   = new DateTime('today');
        $age = $from->diff($to)->y;

        // 60th birthday
        $sixty_birthday = (new DateTime($dob))->modify('+60 years');
        $diff = $to->diff($sixty_birthday);

        $years_left = $diff->y;
        $months_left = $diff->m;
        $days_left = $diff->d;

        ?>
        <h2>Age Report</h2>
        <div class="card shadow p-4 text-center">
            <h4>👤 <?= htmlspecialchars($provider['provider_name']) ?></h4>
            <p><strong>Age:</strong> <?= $age ?> years</p>
            
            <?php if($age < 60): ?>
                <div class="alert alert-success">
                    ✅ You have <strong><?= $years_left ?> years, <?= $months_left ?> months, <?= $days_left ?> days</strong> left for apply as a Seeker.
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                     You are now eligible for Seeker Profile for convert your account as a Seeker please contact Admin on rjharsh4545@gmail.com  (Age: <?= $age ?>).
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    break;
 




    case 'emergency':
    $success = "";
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_emergency'])) {
        $reason = trim($_POST['reason']);
        $file = $_FILES['certificate'];

        if (empty($reason)) {
            $error = "Reason is required.";
        } elseif (empty($file['name'])) {
            $error = "Please upload a certificate.";
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $error = "Invalid file format. Allowed: PDF, JPG, PNG.";
            } else {
                $folder = 'uploads/';
                if (!is_dir($folder)) mkdir($folder, 0777, true);
                $filename = 'cert_' . $provider_id . '_' . time() . '.' . $ext;
                $path = $folder . $filename;

                if (move_uploaded_file($file['tmp_name'], $path)) {
                    $stmt = $conn->prepare("INSERT INTO emergency_conversions (provider_id, reason, certificate_path, request_date) VALUES (?, ?, ?, NOW())");
                    $stmt->bind_param("sss", $provider_id, $reason, $path);
                    $stmt->execute();

                    $success = "✅ Request sent successfully!";
                } else {
                    $error = "❌ Failed to upload certificate.";
                }
            }
        }
    }
    ?>

    <h2>Emergency Medical Conversion (Provider → Seeker)</h2>
    <div class="card shadow p-4">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Reason for Emergency Conversion</label>
                <textarea name="reason" rows="3" class="form-control" required placeholder="Explain your medical emergency..."></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Medical Certificate</label>
                <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" class="form-control" required>
                <small class="text-muted">Allowed formats: PDF, JPG, PNG</small>
            </div>
            <button type="submit" name="submit_emergency" class="btn btn-danger px-4">Send Request</button>
        </form>
    </div>
    <?php
    break;

  case 'report_requests':
    if (!isset($_SESSION['provider_id'])) {
        echo "<div class='alert alert-danger'>Access Denied. Please login.</div>";
        break;
    }

    $provider_id = $_SESSION['provider_id'];
    $from = $_GET['from'] ?? null;
    $to = $_GET['to'] ?? null;

    $data = []; // ✅ Initialize empty data array by default

    // ✅ Fetch data only if both from and to dates are set
    if ($from && $to) {
        $stmt = $conn->prepare("SELECT provider_id, time_slot, service_name, request_date, available_date, city, area 
                                FROM provider_requests 
                                WHERE provider_id = ? 
                                AND request_date BETWEEN ? AND ?
                                ORDER BY request_date DESC");
        $stmt->bind_param("sss", $provider_id, $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    }

    // ✅ PDF download only works if filter is applied
    if (isset($_GET['download']) && $_GET['download'] == 1 && $from && $to) {
        require('fpdf/fpdf.php');

        ob_clean(); // Prevent any previous output
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=provider_request_report.pdf");
        header("Cache-Control: private, max-age=0, must-revalidate");

        class PDF extends FPDF {
            function Header() {
                $this->SetFont('Arial','B',14);
                $this->Cell(0,10,'Provider Request Report',0,1,'C');
                $this->Ln(5);
            }
            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }

        $pdf = new PDF();
        $pdf->AddPage('L');
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40,10,'Provider ID',1);
        $pdf->Cell(35,10,'Time Slot',1);
        $pdf->Cell(45,10,'Service Name',1);
        $pdf->Cell(35,10,'Request Date',1);
        $pdf->Cell(35,10,'Available Date',1);
        $pdf->Cell(35,10,'City',1);
        $pdf->Cell(35,10,'Area',1);
        $pdf->Ln();

        $pdf->SetFont('Arial','',10);
        foreach($data as $row){
            $pdf->Cell(40,10,$row['provider_id'],1);
            $pdf->Cell(35,10,$row['time_slot'],1);
            $pdf->Cell(45,10,$row['service_name'],1);
            $pdf->Cell(35,10,$row['request_date'],1);
            $pdf->Cell(35,10,$row['available_date'],1);
            $pdf->Cell(35,10,$row['city'],1);
            $pdf->Cell(35,10,$row['area'],1);
            $pdf->Ln();
        }

        $pdf->Output("D", "provider_request_report.pdf");
        exit;
    }

    // ✅ HTML output starts here
    ?>
    <h2>Your Request Report</h2>

    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="tab" value="report_requests">
        <div class="col-md-4">
            <label>From Date</label>
            <input type="date" name="from" class="form-control" required value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label>To Date</label>
            <input type="date" name="to" class="form-control" required value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if ($from && $to): ?>
                <a href="?tab=report_requests&from=<?= $from ?>&to=<?= $to ?>&download=1" class="btn btn-success">Download PDF</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($from && $to): ?>
        <?php if ($data): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Provider ID</th>
                            <th>Time Slot</th>
                            <th>Service Name</th>
                            <th>Request Date</th>
                            <th>Available Date</th>
                            <th>City</th>
                            <th>Area</th>
                        </tr> 
                    </thead> 
                    <tbody>
                        <?php foreach($data as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['provider_id']) ?></td>
                                <td><?= htmlspecialchars($row['time_slot']) ?></td>
                                <td><?= htmlspecialchars($row['service_name']) ?></td>
                                <td><?= htmlspecialchars($row['request_date']) ?></td>
                                <td><?= htmlspecialchars($row['available_date']) ?></td>
                                <td><?= htmlspecialchars($row['city']) ?></td>
                                <td><?= htmlspecialchars($row['area']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No records found for selected dates.</div>
        <?php endif; ?>
    <?php endif; ?>

<?php
    if (isset($stmt)) $stmt->close(); // Close stmt if used
    break;
















case 'catalog':
    ?>
    <h2>Catalog of Your Services</h2>
    
    <?php
    
    // Fetch only requests for the logged-in provider
    $stmt = $conn->prepare("SELECT sicker_id, user_name, user_mobile, provider_id, request_type, city, area, slot, rating, time_spent, request_date 
                            FROM requests 
                            WHERE provider_id = ? 
                            ORDER BY request_date DESC");
    $stmt->bind_param("s", $provider_id);
    $stmt->execute();
    $catalog = $stmt->get_result();

    if($catalog->num_rows > 0):

        // ✅ Calculate total time spent
        $total_time = 0;
        foreach($catalog as $row){
            if(!empty($row['time_spent'])){
                $total_time += (float)$row['time_spent'];
            }
        }
        // Reset result pointer for table loop
        $catalog->data_seek(0);
        ?>
        
        <!-- Show total time spent -->
        <div class="alert alert-info">
            <strong>Total Time Spent:</strong> <?= $total_time ?> hrs
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Seeker ID</th>
                    <th>Name</th>
                    <th>Mobile</th> 
                    <th>Service</th>
                    <th>City</th>
                    <th>Area</th>
                    <th>Slot</th>
                    <th>Rating</th>
                    <th>Time Spent (hrs)</th>
                    <th>Request Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $catalog->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['sicker_id']) ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['user_mobile']) ?></td>
                    <td><?= htmlspecialchars($row['request_type']) ?></td>
                    <td><?= htmlspecialchars($row['city']) ?></td>
                    <td><?= htmlspecialchars($row['area']) ?></td>
                    <td><?= htmlspecialchars($row['slot']) ?></td>
                    <td><?= isset($row['rating']) ? $row['rating'] : 'N/A' ?></td>
                    <td><?= isset($row['time_spent']) ? $row['time_spent'] : 'N/A' ?></td>
                    <td><?= date('d-m-Y', strtotime($row['request_date'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No requests found for your account.</div>
    <?php endif;
    break;
 
















    case 'profile':
        $cities = $conn->query("SELECT id, city_name FROM cities ORDER BY city_name ASC")->fetch_all(MYSQLI_ASSOC);

        ?>
        
        <h2>Update Profile</h2>
        <form method="POST" enctype="multipart/form-data" class="card p-3 shadow w-75">
           
        <input type="hidden" name="update_profile" value="1">
           
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($provider['image']) ?>">

            <div class="mb-3 text-center">
                <?php $img = (!empty($provider['image']) && file_exists($provider['image'])) ? $provider['image'] : "uploads/default.png"; ?>
                <img src="<?= $img ?>" id="preview" class="rounded-circle mb-2" style="width:120px;height:120px;object-fit:cover;border:2px solid #ccc;">
                
                 <br >Change Profile:
                 <input type="file" name="image" class="form-control form-control-sm mt-2" accept="image/*">
           
            </div>
<div class="mb-3">
    <label>Provider ID</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($provider['provider_id']) ?>" readonly>
</div>

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="provider_name" class="form-control" value="<?= htmlspecialchars($provider['provider_name']) ?>" required>
            </div>
           <?php
// Calculate age from DOB
$dob = $provider['provider_dob'] ?? null;
$age = '';
if ($dob) {
    $from = new DateTime($dob);
    $to = new DateTime('today');
    $age = $from->diff($to)->y;
}
?>

<div class="mb-3">
    <label>Date of Birth</label>
    <input type="date" class="form-control" value="<?= htmlspecialchars($dob) ?>" readonly>
</div>

<div class="mb-3">
    <label>Age</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($age . ' years') ?>" readonly>
</div>


            <div class="mb-3">
                <label>Gender</label><br>
                <input type="radio" name="provider_gender" value="Male" <?= $provider['provider_gender']=='Male'?'checked':'' ?>> Male
                <input type="radio" name="provider_gender" value="Female" <?= $provider['provider_gender']=='Female'?'checked':'' ?>> Female
                </div>
            <div class="mb-3">
                <label>Mobile</label>
                <input type="text" name="provider_mobile" class="form-control" value="<?= htmlspecialchars($provider['provider_mobile']) ?>" required>
            </div>
          <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($provider['email'] ?? '') ?>" required>
</div>

  <div class="mb-3">
    <label>City</label>
    <select name="city" id="city" class="form-select" required>
        <option value="">Select City</option>
        <?php 
        $cities = $conn->query("SELECT DISTINCT city_name FROM areas ORDER BY city_name ASC");
        while($c=$cities->fetch_assoc()): 
            $selected = ($provider['city']==$c['city_name'])?'selected':'';
        ?>
            <option value="<?= htmlspecialchars($c['city_name']) ?>" <?= $selected ?>><?= htmlspecialchars($c['city_name']) ?></option>
        <?php endwhile; ?>
    </select>
</div>



<div class="mb-3">
    <label>Area</label>
    <select name="area" id="area" class="form-select" required>
        <option value="">Select Area</option>
        <?php 
        if(!empty($provider['city'])){
            $city = $conn->real_escape_string($provider['city']);
            $areas = $conn->query("SELECT area_name FROM areas WHERE city_name='$city' ORDER BY area_name ASC");
            while($a=$areas->fetch_assoc()):
                $selected = ($provider['area']==$a['area_name'])?'selected':'';
                echo '<option value="'.htmlspecialchars($a['area_name']).'" '.$selected.'>'.htmlspecialchars($a['area_name']).'</option>';
            endwhile;
        }
        ?>
    </select>
</div>


<div class="mb-3">
    <label>Password</label>
    <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($provider['password']) ?>" required>
</div>

<div class="mb-3">
    <label>Aadhaar Document</label><br>
    <?php if(!empty($provider['aadhar_doc']) && file_exists($provider['aadhar_doc'])): ?>
        <a href="<?= $provider['aadhar_doc'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
            View Aadhaar
        </a><br><br>
    <?php endif; ?>
    <input type="hidden" name="existing_aadhar" value="<?= htmlspecialchars($provider['aadhar_doc']) ?>">
    <input type="file" name="aadhar_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
</div>


            <button type="submit" class="btn btn-success">Update Profile</button><br>
            <div class="alert alert-warning">
    <strong>Note:</strong> If you want to change your Date of Birth then contact to admin at: rjharsh4545@gmail.com
</div>

        </form>
        <?php
        break;

    case 'add_request':
    ?>
    <h2>Add Request</h2>
    <?php if(isset($success_msg)) echo "<div class='alert alert-success'>$success_msg</div>"; ?>
    <form method="POST" onsubmit="return confirmRequest();">

        <div class="mb-3">
            <label>Select Date</label>
            <input type="date" name="available_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
        </div>

        <!-- City Dropdown -->
        <div class="mb-3">
            <label>Select City</label>
            <select name="city" id="req_city" class="form-select" required>
                <option value="">Select City</option>
                <?php 
                $cities = $conn->query("SELECT city_name FROM cities ORDER BY city_name ASC");
                while($c=$cities->fetch_assoc()){
                    echo '<option value="'.htmlspecialchars($c['city_name']).'">'.htmlspecialchars($c['city_name']).'</option>';
                }
                ?>
            </select>
        </div>

        <!-- Area Dropdown -->
        <div class="mb-3">
            <label>Select Area</label>
            <select name="area" id="req_area" class="form-select" required>
                <option value="">Select Area</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Select Slot</label>
            <select name="time_slot" class="form-select" required>
                <option value="">Select Slot</option>
                <?php foreach($slots as $s) echo "<option>".htmlspecialchars($s['time_range'])."</option>"; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Select Services</label>
            <?php foreach($services_list as $srv): ?>
                <div class="form-check">
                    <input type="checkbox" name="services[]" value="<?= htmlspecialchars($srv['service_name']) ?>" class="form-check-input" id="srv_<?= $srv['id'] ?>">
                    <label for="srv_<?= $srv['id'] ?>" class="form-check-label"><?= htmlspecialchars($srv['service_name']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>
<script>
function confirmRequest() {
    return confirm("You want to send this request? Please check all details before submitting.");
}
</script>

        <button type="submit" name="add_request" class="btn btn-primary">Submit Request</button>
    </form>
    <?php
    break;


    case 'received_requests':
        ?>
        <h2>Received Requests</h2>
        <?php if($requests->num_rows>0): ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th><th>Sicker</th><th>Mobile</th><th>Service</th><th>Slot</th><th>City</th><th>Area</th><th>Date</th><th>Note</th><th>Rating</th><th>Status</th><th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while($r=$requests->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['request_id'] ?></td>
                    <td><?= htmlspecialchars($r['user_name']) ?></td>
                    <td><?= htmlspecialchars($r['user_mobile']) ?></td>
                    <td><?= htmlspecialchars($r['request_type']) ?></td>
                    <td><?= htmlspecialchars($r['slot']) ?></td>
                    <td><?= htmlspecialchars($r['city']) ?></td>
                    <td><?= htmlspecialchars($r['area']) ?></td>
                    <td><?= date('d-m-Y', strtotime($r['request_date'])) ?></td>
                    <td><?= !empty($r['note']) ? nl2br(htmlspecialchars($r['note'])) : '-' ?></td>
                    <td><?= isset($r['rating'])?str_repeat("★",$r['rating']).str_repeat("☆",5-$r['rating']):'N/A' ?></td>
                    <td><span class="badge bg-<?= $r['status']=='Pending'?'warning':($r['status']=='Accepted'?'success':'danger')?>"><?= $r['status'] ?></span></td>
                    <td>
                        <select class="form-select form-select-sm change-status" data-id="<?= $r['request_id'] ?>">
    <option value="Pending" <?= $r['status']=='Pending'?'selected':''?>>Pending</option>
    <option value="Accepted" <?= $r['status']=='Accepted'?'selected':''?>>Accepted</option>
    <option value="Rejected" <?= $r['status']=='Rejected'?'selected':''?>>Rejected</option>
</select>

                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info">No requests found.</div>
        <?php endif;
        break;

    case 'prodate':
    ?>
    <h2>Date Report</h2>
    <form method="GET" class="mb-3">
        <input type="hidden" name="tab" value="prodate">
        <label>Select Date</label>
        <input type="date" name="date" class="form-control" required>
        <button type="submit" class="btn btn-primary mt-2">Generate</button>
    </form>
    <?php

    if(isset($_GET['date'])){
        $date = $_GET['date'];
        $stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id=? AND request_date=? ORDER BY request_date DESC");
        $stmt->bind_param("ss", $provider_id, $date);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows > 0){
            // ✅ Capture report HTML
            ob_start();
            echo "<h4 class='mt-3'>Report for ".htmlspecialchars($date)."</h4>";
            echo "<table class='table table-bordered'><tr><th>ID</th><th>Sicker</th><th>Service</th><th>Slot</th><th>Note</th><th>Status</th></tr>";
            while($r=$res->fetch_assoc()){
                $note_display = !empty($r['note']) ? nl2br(htmlspecialchars($r['note'])) : '-';
                echo "<tr>
                    <td>{$r['request_id']}</td>
                    <td>".htmlspecialchars($r['user_name'])."</td>
                    <td>".htmlspecialchars($r['request_type'])."</td>
                    <td>".htmlspecialchars($r['slot'])."</td>
                    <td>{$note_display}</td>
                    <td>".htmlspecialchars($r['status'])."</td>
                </tr>";
            }
            echo "</table>";

            $report_html = ob_get_clean();

            // ✅ Display the report on screen
            echo $report_html;

            // ✅ Download Button
            echo "<form method='post' action='download_report.php' target='_blank'>
                    <input type='hidden' name='date' value='".htmlspecialchars($date)."'>
                    <button type='submit' class='btn btn-success mt-2'>⬇ Download PDF</button>
                  </form>";
        } else { 
            echo "<div class='alert alert-info'>No records found for this date.</div>"; 
        }
    }
    break;


   case 'promonth':
    ?>
    <h2>Month Report</h2>
    <form method="GET" class="mb-3">
        <input type="hidden" name="tab" value="promonth">
        <label>Select Month</label>
        <input type="month" name="month" class="form-control" required>
        <button type="submit" class="btn btn-primary mt-2">Generate</button>
    </form>
    <?php

    if(isset($_GET['month'])){
        $month = $_GET['month'];
        $stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id=? AND DATE_FORMAT(request_date,'%Y-%m')=? ORDER BY request_date DESC");
        $stmt->bind_param("ss", $provider_id, $month);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows > 0){
            // ✅ Capture report HTML
            ob_start();
            echo "<h4 class='mt-3'>Report for ".htmlspecialchars($month)."</h4>";
            echo "<table class='table table-bordered'>
                    <tr>
                        <th>ID</th><th>Sicker</th><th>Service</th><th>Slot</th><th>Date</th><th>Note</th><th>Status</th>
                    </tr>";
            while($r=$res->fetch_assoc()){
                $note_display = !empty($r['note']) ? nl2br(htmlspecialchars($r['note'])) : '-';
                echo "<tr>
                        <td>{$r['request_id']}</td>
                        <td>".htmlspecialchars($r['user_name'])."</td>
                        <td>".htmlspecialchars($r['request_type'])."</td>
                        <td>".htmlspecialchars($r['slot'])."</td>
                        <td>".date('d-m-Y',strtotime($r['request_date']))."</td>
                        <td>{$note_display}</td>
                        <td>".htmlspecialchars($r['status'])."</td>
                    </tr>";
            }
            echo "</table>";

            $report_html = ob_get_clean();

            // ✅ Show report on screen
            echo $report_html;

            // ✅ Download button
            echo "<form method='post' action='download_report1.php' target='_blank'>
                    <input type='hidden' name='month' value='".htmlspecialchars($month)."'>
                    <input type='hidden' name='type' value='month'>
                    <button type='submit' class='btn btn-success mt-2'>⬇ Download PDF</button>
                  </form>";
        } else {
            echo "<div class='alert alert-info'>No records found for this month.</div>";
        }
    }
    break;


    case 'proyr':
    ?>
    <h2>Year Report</h2>
    <form method="GET" class="mb-3">
        <input type="hidden" name="tab" value="proyr">
        <label>Select Year</label>
        <input type="number" name="year" class="form-control" min="2000" max="<?=date('Y')?>" required>
        <button type="submit" class="btn btn-primary mt-2">Generate</button>
    </form>
    <?php

    if(isset($_GET['year'])){
        $year = $_GET['year'];
        $stmt = $conn->prepare("SELECT * FROM requests WHERE provider_id=? AND YEAR(request_date)=? ORDER BY request_date DESC");
        $stmt->bind_param("ss", $provider_id, $year);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows > 0){
            // ✅ Capture HTML
            ob_start();
            echo "<h4 class='mt-3'>Report for Year ".htmlspecialchars($year)."</h4>";
            echo "<table class='table table-bordered'>
                    <tr>
                        <th>ID</th><th>Sicker</th><th>Service</th><th>Slot</th><th>Date</th><th>Note</th><th>Status</th>
                    </tr>";
            while($r=$res->fetch_assoc()){
                $note_display = !empty($r['note']) ? nl2br(htmlspecialchars($r['note'])) : '-';
                echo "<tr>
                        <td>{$r['request_id']}</td>
                        <td>".htmlspecialchars($r['user_name'])."</td>
                        <td>".htmlspecialchars($r['request_type'])."</td>
                        <td>".htmlspecialchars($r['slot'])."</td>
                        <td>".date('d-m-Y',strtotime($r['request_date']))."</td>
                        <td>{$note_display}</td>
                        <td>".htmlspecialchars($r['status'])."</td>
                    </tr>";
            }
            echo "</table>";

            $report_html = ob_get_clean();

            // ✅ Display report
            echo $report_html;

            // ✅ Add Download button
            echo "<form method='post' action='download_report2.php' target='_blank'>
                    <input type='hidden' name='year' value='".htmlspecialchars($year)."'>
                    <input type='hidden' name='type' value='year'>
                    <button type='submit' class='btn btn-success mt-2'>⬇ Download PDF</button>
                  </form>";
        } else {
            echo "<div class='alert alert-info'>No records found for this year.</div>";
        }
    }
    break;


    default:
        echo "<h2>Dashboard</h2><p>Welcome to your provider dashboard.</p>";
}
?>
</div>
</div>

<script>



// For Add Request city-area dropdown
$(document).ready(function(){
    $('#req_city').change(function(){
        let city_name = $(this).val();
        if(city_name){
            $.post("get_areas_request.php",{city_name:city_name},function(data){
                $('#req_area').html(data);
            });
        } else {
            $('#req_area').html('<option value="">Select Area</option>');
        }
    });
});

  $(document).ready(function(){
    $('#city').change(function(){
        let city_name = $(this).val();
        if(city_name){
            $.post("get_areas1.php",{city_name:city_name},function(data){
                $('#area').html(data);
            });
        } else {
            $('#area').html('<option value="">Select Area</option>');
        }
    });
});



$(document).ready(function(){
    $('.change-status').change(function(){
        let requestId=$(this).data('id');
        let status=$(this).val();
        $.post('provider_dashboard.php?tab=received_requests',{ajax:'update_status',request_id:requestId,status:status},function(resp){
            const json=JSON.parse(resp);
            if(json.success){ alert("Status updated successfully!"); location.reload();}
            else alert("Failed to update status.");
        });
    });

    // Profile image preview
    $("input[name='image']").on('change', function(){
        const [file] = this.files;
        if(file){
            $('#preview').attr('src', URL.createObjectURL(file));
        }
    });
});
 

//received req
$(document).ready(function(){
    $(".change-status").on("change", function(){
        let request_id = $(this).data("id");
        let new_status = $(this).val();
        let row = $(this).closest("tr");

        $.ajax({
            url: "update_request_status.php",
            type: "POST",
            data: { request_id: request_id, status: new_status },
            success: function(response){
                try {
                    let res = JSON.parse(response);
                    if(res.success){
                        // ✅ Update badge instantly
                        let badge = row.find("td:nth-child(11) span");
                        badge
                            .removeClass("bg-warning bg-success bg-danger")
                            .addClass(
                                new_status === "Pending" ? "bg-warning" :
                                new_status === "Accepted" ? "bg-success" : "bg-danger"
                            )
                            .text(new_status);

                        // ✅ Also update dropdown selection
                        row.find(".change-status").val(new_status);
                    } else {
                        alert("❌ " + res.message);
                    }
                } catch(e){
                    alert("⚠️ Unexpected response: " + response);
                }
            },
            error: function(){
                alert("⚠️ Failed to update status. Please try again.");
            }
        });
    });
});




</script>
</body>
</html>
<script>
$(document).ready(function(){
    $('.change-status').on('change', function(){
        var request_id = $(this).data('id');
        var new_status = $(this).val();

        $.ajax({
            url: 'update_status2.php',
            type: 'POST',
            data: {
                request_id: request_id,
                status: new_status
            },
            success: function(response){
                if(response.trim() === "success"){
                    alert("Status updated successfully!");
                    location.reload(); // OR update badge text if needed
                } else {
                    alert("Error updating status: " + response);
                }
            },
            error: function(){
                alert("AJAX error occurred.");
            }
        });
    });
});
</script>
 