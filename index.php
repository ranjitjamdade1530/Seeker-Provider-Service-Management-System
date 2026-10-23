<?php
include 'db.php';
$total_count = $conn->query("SELECT COUNT(*) as total FROM requests")->fetch_assoc()['total'];
$accepted_count = $conn->query("SELECT COUNT(*) as total FROM requests WHERE status = 'Accepted'")->fetch_assoc()['total'];


// Fetch dropdown values
$sicker_count = $conn->query("SELECT COUNT(*) as total FROM sickers")->fetch_assoc()['total'];
$provider_count = $conn->query("SELECT COUNT(*) as total FROM providers")->fetch_assoc()['total'];

$cities = $conn->query("SELECT city_name FROM cities ORDER BY city_name");
$services = $conn->query("SELECT service_name FROM request_services ORDER BY service_name");
$slots = $conn->query("SELECT time_range FROM time_slots ORDER BY time_range");

$providers = [];
if (isset($_POST['search_provider'])) {
    $request_date = $_POST['request_date'];
    $city = $_POST['city'];
    $area = $_POST['area'];
    $request_type = $_POST['request_type'];
    $time_slot = $_POST['time_slot'];

    $sql = "SELECT p.provider_id, p.provider_name,  p.provider_gender,
               p.provider_mobile,
               pr.city, pr.area,
               pr.service_name, pr.time_slot, pr.available_date
        FROM providers p
        INNER JOIN provider_requests pr ON p.provider_id = pr.provider_id
        WHERE pr.city = ? AND pr.area = ?
          AND pr.service_name = ?
          AND pr.time_slot = ?
          AND pr.available_date >= ?";


    $stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("sssss", $city, $area, $request_type, $time_slot, $request_date);
$stmt->execute();
$providers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
  <meta charset="UTF-8">
  <title>Service Management System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    .header img { width: 100%; height: 250px; object-fit: cover; }
    footer { background-color: #343a40; color: #fff; padding: 20px 0; text-align: center; }
    .main-content { min-height: 400px; padding: 40px 20px; }
  </style>
</head>
<body>

<!-- Header -->
<div class="container">
  <div class="header">
     <img src="img/h1.png" alt="Header Image" style="width: 100%; height: 300px;">

  </div>
</div>
<br> 
 
<!-- Navbar -->
<div class="container">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">Service Management</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin Login</a></li>
          <li class="nav-item"><a class="nav-link" href="provider_login.php">Provider Login</a></li>
          <li class="nav-item"><a class="nav-link" href="sicker_login.php">Seeker Login</a></li>
          <li class="nav-item"><a class="nav-link" href="add_sicker1.php">Register Seeker</a></li>
          <li class="nav-item"><a class="nav-link" href="add_provider1.php">Register Provider</a></li>
        </ul>
      </div>
    </div>
  </nav>
</div>

<!-- Counters -->
<div class="row justify-content-center my-4">
  <div class="col-md-4">
    <div class="card text-center shadow bg-info text-white">
      <div class="card-body">
        <h5>Total Seeker</h5>
        <h2 class="counter" data-target="<?= $sicker_count ?>">0</h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center shadow bg-info text-white">
      <div class="card-body">
        <h5>Total Providers</h5>
        <h2 class="counter" data-target="<?= $provider_count ?>">0</h2>
      </div>
    </div>
  </div>
</div>


<!-- Total Requests -->
<div class="row justify-content-center my-4">
  <div class="col-md-4">
    <div class="card text-center shadow bg-success text-white">
      <div class="card-body">
        <h5>Total Requests</h5>
       <h2 class="counter" data-target="<?= $total_count ?>"><span class="count">0</h2>

      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center shadow">
      <div class="card-body bg-success text-white">
        <h5>Completed Requests</h5>
   <h2 class="counter" data-target="<?= $accepted_count ?>">
  <span class="count">0</span> 
</h2>




      </div>
    </div>
  </div>
</div>
<!-- Search Form -->
<div class="container main-content">
  <h3 class="text-center mb-4">Search Provider</h3>
  <center>
    <form method="POST" action="" class="card p-4 shadow mb-4" style="width: 500px;">
      <div class="mb-3">
        <label>Request Date</label>
        <input type="date" name="request_date" id="request_date" class="form-control"
               min="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="mb-3">
        <label>City</label>
        <select name="city" id="city" class="form-select" required>
          <option value="">Select City</option>
          <?php while($c = $cities->fetch_assoc()): ?>
            <option value="<?= $c['city_name'] ?>"><?= $c['city_name'] ?></option>
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
        <label>Request Type</label>
        <select name="request_type" class="form-select" required>
          <option value="">Select Service</option>
          <?php while($s = $services->fetch_assoc()): ?>
            <option value="<?= $s['service_name'] ?>"><?= $s['service_name'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Time Slot</label>
        <select name="time_slot" class="form-select" required>
          <option value="">Select Slot</option>
          <?php while($t = $slots->fetch_assoc()): ?>
            <option value="<?= $t['time_range'] ?>"><?= $t['time_range'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="text-center">
        <button type="submit" name="search_provider" class="btn btn-primary w-50">Search</button>
      </div>
    </form>
  </center>

  <!-- Provider Table / Message -->
  <?php if (isset($_POST['search_provider'])): ?>
    <?php if (!empty($providers)): ?>
      <h4 class="mb-3">Available Providers</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-hover shadow">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Mobile</th>
              <th>City</th>
              <th>Area</th>
              <th>Gender/Age</th>
              <th>Service</th>
              <th>Time Slot</th>
              <th>Available Date</th>
              <th>Request</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($providers as $p): ?>
              <tr>
                <td><?= $p['provider_id'] ?></td>
                <td><?= htmlspecialchars($p['provider_name']) ?></td>
                <td><?= htmlspecialchars($p['provider_mobile']) ?></td>
                <td><?= htmlspecialchars($p['city']) ?></td>
                <td><?= htmlspecialchars($p['area']) ?></td>
                <td><?= htmlspecialchars($p['provider_gender']) ?>/<?= htmlspecialchars($p['provider_age']) ?></td>
                <td><?= htmlspecialchars($p['service_name']) ?></td>
                <td><?= htmlspecialchars($p['time_slot']) ?></td>
                <td><?= htmlspecialchars($p['available_date']) ?></td>
                <td>
                  <form method="POST" action="sicker_login.php">
                    <input type="hidden" name="provider_id" value="<?= $p['provider_id'] ?>">
                    <input type="hidden" name="service_name" value="<?= htmlspecialchars($p['service_name']) ?>">
                    <input type="hidden" name="time_slot" value="<?= htmlspecialchars($p['time_slot']) ?>">
                    <input type="hidden" name="request_date" value="<?= htmlspecialchars($request_date) ?>">
                    <input type="hidden" name="available_date" value="<?= htmlspecialchars($p['available_date']) ?>">
                    <button type="submit" class="btn btn-sm btn-success">Send</button>
                  </form>
                </td> 
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-warning text-center shadow mt-4">
        <strong>Provider are not allowed as per your interest..!</strong>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<!-- Footer -->
<div class="container">
  <footer>
    &copy; <?= date('Y') ?> Service Management System.
  </footer>
</div>

<!-- JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Dynamic Area Script -->
<script>
$('#city').on('change', function () {
    const selectedCity = $(this).val();
    $('#area').html('<option>Loading...</option>');

    $.post('area_fetch.php', { city: selectedCity }, function (res) {
        let html = '<option value="">Select Area</option>';
        if (res.success) {
            res.areas.forEach(function (area) {
                html += `<option value="${area}">${area}</option>`;
            });
        } else {
            html = '<option>No areas found</option>';
        }
        $('#area').html(html);
    }, 'json');
});
</script>

<!-- Counter Animation -->
<script>
const counters = document.querySelectorAll('.counter');
counters.forEach(counter => {
  counter.innerText = '0';
  const updateCounter = () => {
    const target = +counter.getAttribute('data-target');
    const count = +counter.innerText;
    const increment = Math.ceil(target / 100);

    if (count < target) {
      counter.innerText = count + increment;
      setTimeout(updateCounter, 30);
    } else {
      counter.innerText = target;
    }
  };
  updateCounter();
});
</script>


<!----->

</body>
</html>
