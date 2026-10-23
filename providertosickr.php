<?php
require 'db.php';
session_start();

$converted = []; // store converted providers for display

// Fetch all eligible providers (age >= 60 and not yet converted)
$providers = $conn->query("SELECT * FROM providers WHERE converted = 0");
$eligible = [];

while($provider = $providers->fetch_assoc()) {
    $dob = $provider['provider_dob'];
    if (!$dob) continue;

    // Calculate age
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;

    if ($age >= 60) {
        $provider['age'] = $age;
        $eligible[] = $provider;
    }
}

// Handle conversion of one provider
if(isset($_POST['convert']) && isset($_POST['provider_id'])) {
    $provider_id = $_POST['provider_id'];

    // Fetch provider
    $stmt = $conn->prepare("SELECT * FROM providers WHERE provider_id=?");
    $stmt->bind_param("s", $provider_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $provider = $result->fetch_assoc();
    $stmt->close();

    if($provider) {
        $age = (new DateTime($provider['provider_dob']))->diff(new DateTime('today'))->y;
        $request_date = $provider['date'];

        // Generate new Sicker ID
        $lastSicker = $conn->query("SELECT sicker_id FROM sickers ORDER BY id DESC LIMIT 1");
        if ($lastSicker->num_rows > 0) {
            $last_id = $lastSicker->fetch_assoc()['sicker_id'];
            $number = intval(substr($last_id, 1)) + 1;
        } else {
            $number = 1;
        }
        $new_sicker_id = 'S' . str_pad($number, 3, '0', STR_PAD_LEFT);

        // Insert into sickers table
        $stmt = $conn->prepare("INSERT INTO sickers 
            (sicker_id, sicker_name, dob, sicker_age, sicker_gender, sicker_mobile, city, area, request_date, password, image, aadhar_doc, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssisssssssss",
            $new_sicker_id,
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
            $provider['status']
        );
        $stmt->execute();
        $stmt->close();

        // ✅ Mark provider as converted
        $conn->query("UPDATE providers SET converted = 1 WHERE provider_id = '$provider_id'");

        // Add to converted list to display
       $converted[] = [
    'provider_id' => $provider['provider_id'],
    'sicker_id' => $new_sicker_id,
    'name' => $provider['provider_name'],
    'email' => $provider['email'] ?? '', // Added
    'password' => $provider['password']
];

        // Remove from eligible list
        foreach($eligible as $k => $p) {
            if($p['provider_id'] == $provider_id) unset($eligible[$k]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Convert Providers to Sickers</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Eligible Providers </h2>

    <?php if(!empty($eligible)): ?>
        <table class="table table-bordered table-striped mt-3">
            <thead class="table-dark">
    <tr>
        <th>Provider ID</th>
        <th>Name</th>
        <th>Email</th> <!-- Added -->
        <th>DOB</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Mobile</th>
        <th>City</th>
        <th>Area</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
    <?php foreach($eligible as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['provider_id']) ?></td>
        <td><?= htmlspecialchars($p['provider_name']) ?></td>
        <td><?= htmlspecialchars($p['email'] ?? '') ?></td> <!-- Added -->
        <td><?= htmlspecialchars($p['provider_dob']) ?></td>
        <td><?= htmlspecialchars($p['age']) ?></td>
        <td><?= htmlspecialchars($p['provider_gender']) ?></td>
        <td><?= htmlspecialchars($p['provider_mobile']) ?></td>
        <td><?= htmlspecialchars($p['city']) ?></td>
        <td><?= htmlspecialchars($p['area']) ?></td>
        <td>
            <form method="POST" onsubmit="return confirm('Convert this provider to sicker?');">
                <input type="hidden" name="provider_id" value="<?= $p['provider_id'] ?>">
                </form>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    <?php else: ?>
        <div class="alert alert-info mt-3">No providers eligible for conversion.</div>
    <?php endif; ?>

    <?php if(!empty($converted)): ?>
        <h2 class="mt-5">Newly Converted Sickers</h2>
        <table class="table table-bordered table-striped">
           <thead class="table-dark">
    <tr>
        <th>Provider ID</th>
        <th>Sicker ID</th>
        <th>Name</th>
        <th>Email</th> <!-- Added -->
        <th>Password</th>
    </tr>
</thead>
<tbody> 
    <?php foreach($converted as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['provider_id']) ?></td>
        <td><?= htmlspecialchars($c['sicker_id']) ?></td>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= htmlspecialchars($c['email'] ?? '') ?></td> <!-- Added -->
        <td><?= htmlspecialchars($c['password']) ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    <?php endif; ?>
</div>
</body>
</html>
