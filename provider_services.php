<?php
include 'db.php';

// Fetch all providers
$providers = $conn->query("
    SELECT p.provider_id, p.provider_name, p.services, c.city_name, p.area
    FROM providers p
    LEFT JOIN cities c ON p.city = c.id
    ORDER BY p.provider_name ASC
");

if(!$providers){
    die("Query Error: ".$conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Provider Services</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
<h3 class="text-center mb-4">All Services Provided by Providers</h3>

<div class="card p-4 shadow">
<table class="table table-bordered">
<thead>
<tr>
<th>Provider ID</th>
<th>Provider Name</th>
<th>City</th>
<th>Area</th>
<th>Services Provided</th>
</tr>
</thead>
<tbody>
<?php while($p = $providers->fetch_assoc()): ?>
<tr>
<td><?= $p['provider_id'] ?></td>
<td><?= htmlspecialchars($p['provider_name']) ?></td>
<td><?= htmlspecialchars($p['city_name']) ?></td>
<td><?= htmlspecialchars($p['area']) ?></td>
<td>
<?php
// Split services by comma and display as list
$services = explode(',', $p['services']);
foreach($services as $s){
    echo '<span class="badge bg-primary me-1">'.htmlspecialchars(trim($s)).'</span>';
}
?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>
