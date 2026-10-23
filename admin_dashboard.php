<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['admin_id'] = 1; // Simulate login
    $_SESSION['admin_name'] = "Admin";
}
require 'db.php';

// ✅ Handle Approve / Reject actions
if (isset($_GET['type'], $_GET['id'], $_GET['status'])) {
    $type   = $_GET['type'];
    $id     = $_GET['id'];
    $status = $_GET['status'];

    if ($type === "sicker") {
        $conn->query("UPDATE sickers SET status='$status' WHERE sicker_id='$id'");
    } elseif ($type === "provider") {
        $conn->query("UPDATE providers SET status='$status' WHERE provider_id='$id'");
    }
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root { --sidebar-width: 250px; --navbar-height: 56px; }
    body { min-height: 100vh; background-color: #f8f9fa; margin: 0; }
    .sidebar {
      position: fixed; inset: 0 auto 0 0;
      width: var(--sidebar-width); height: 100vh;
      background-color: #212529; padding-top: 20px; overflow-y: auto;
    }
    .sidebar a { color:#fff; padding:10px 20px; display:block; text-decoration:none; }
    .sidebar a:hover, .sidebar a.active { background-color:#0d6efd; }
    .navbar { margin-left: var(--sidebar-width); height: var(--navbar-height); }
    .main-content { margin-left: var(--sidebar-width); padding:20px; }
    .frame-wrap { position: fixed; left:var(--sidebar-width); right:0; top:var(--navbar-height); bottom:0; padding:20px; overflow:hidden; }
    .content-frame { width:100%; height:100%; border:0; border-radius:.5rem; background:#fff; box-shadow:0 0 12px rgba(0,0,0,.06); display:none; }
    .loading-overlay {
      position:absolute; inset:20px; display:none; align-items:center; justify-content:center;
      background:repeating-linear-gradient(45deg,rgba(13,110,253,.03),rgba(13,110,253,.03) 10px,rgba(13,110,253,.06) 10px,rgba(13,110,253,.06) 20px);
      border-radius:.5rem;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebarMenu">
  <h4 class="text-center text-white">Admin Panel</h4>
</div>

<!-- Navbar --> 
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <span class="navbar-brand">Welcome <?php echo htmlspecialchars($_SESSION['admin_name'] ?? ''); ?></span>
  </div>
</nav>
 
<!-- Main Content -->
<div class="main-content">
  <div id="dashboardSection">
  <h4 class="mb-3">Dashboard</h4>
  <p>Welcome to your admin dashboard. Below is an overview of all accounts:</p>

  <!-- Sickers Table -->
  <h5 class="text-primary mt-4">All Seeker</h5>
  <div class="table-responsive mb-4">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Password</th>
          <th>Email</th>

          <th>Age</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sickerResult = $conn->query("SELECT sicker_id, sicker_name, password, sicker_age ,email FROM sickers ORDER BY sicker_id ASC");
        if ($sickerResult->num_rows > 0) {
          while($row = $sickerResult->fetch_assoc()){
            echo "<tr>
                    <td>{$row['sicker_id']}</td>
                    <td>".htmlspecialchars($row['sicker_name'])."</td>
                    <td>".htmlspecialchars($row['password'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>

                    <td>".htmlspecialchars($row['sicker_age'])."</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='4' class='text-center'>No Sickers found</td></tr>";
        }
        ?> 
      </tbody>
    </table>
  </div>

  <!-- Providers Table -->
  <h5 class="text-success mt-4">All Providers</h5>
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Password</th>
          <th>Email</th>

          <th>Age</th>
        </tr>
      </thead>
      <tbody> 
<?php
$providerResult = $conn->query("SELECT provider_id, provider_name, password, provider_dob, email FROM providers ORDER BY provider_id ASC");
if ($providerResult->num_rows > 0) {
  while($row = $providerResult->fetch_assoc()){
    // Calculate age from DOB
    $dob = new DateTime($row['provider_dob']);
    $today = new DateTime('today');
    $age = $dob->diff($today)->y;

    echo "<tr>
            <td>{$row['provider_id']}</td>
            <td>".htmlspecialchars($row['provider_name'])."</td>
            <td>".htmlspecialchars($row['password'])."</td>
            <td>".htmlspecialchars($row['email'])."</td>

            <td>{$age} </td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='4' class='text-center'>No Providers found</td></tr>";
}
?>
</tbody>

    </table>
  </div>
</div>


</div>

<!-- Iframe Region -->
<div class="frame-wrap">
  <div id="loadingOverlay" class="loading-overlay">
    <div class="text-muted d-flex align-items-center gap-2">
      <div class="spinner-border" role="status"></div>
      <span>Loading…</span>
    </div>
  </div>
  <iframe id="contentFrame" class="content-frame" src="about:blank"></iframe>
</div>

<!-- JavaScript -->
<script> 
const menuItems = [
  { name: "Dashboard", type: "section", target: "dashboardSection" },
  { name: "Active Account", type: "load", url: "activeaccount.php" },
  { name: "Converted Providers", type: "load", url: "converted_info.php" },
  { name: "Emergency Conversion Into Seeker ", type: "load", url: "admin_emergency_requests.php" },
 
  { name: "Register Seeker", type: "load", url: "add_sicker.php" },
  { name: " Register Provider", type: "load", url: "add_provider.php" },
  { name: "City", type: "load", url: "add_city.php" },
  { name: "Manage City", type: "load", url: "manage_city.php" },
  { name: "Area", type: "load", url: "area.php" },
  { name: "Manage Area", type: "load", url: "manage_area.php" },
  { name: "Time Slot", type: "load", url: "manage_slot.php" },
  { name: "Services Type", type: "load", url: "servicetype.php" },
  { name: "Manage Seeker", type: "load", url: "update_sicker.php" },
  { name: "Manage Provider", type: "load", url: "update_provider.php" },
  { name: "All Requests", type: "load", url: "all_requests.php" },
  { name: "All Providers Report", type: "load", url: "providers_report.php" },
  { name: "All Seekers Report", type: "load", url: "sickers_report.php" },
  { name: "All Requests Report", type: "load", url: "requests_report.php" },

  { name: "Logout", type: "link", url: "logout2.php", icon: "bi bi-box-arrow-right" }
];

const contentFrame = document.getElementById('contentFrame');
const loadingOverlay = document.getElementById('loadingOverlay');

function buildSidebar() {
  const sidebar = document.getElementById("sidebarMenu");
  menuItems.forEach(item => {
    const link = document.createElement("a");
    link.href = item.type === "link" ? item.url : "#";
    if (item.icon) link.innerHTML = `<i class="${item.icon}"></i> ${item.name}`;
    else link.textContent = item.name;

    link.addEventListener('click', (e) => {
      if (item.type !== "link") e.preventDefault();
      if (item.type === "section") showSection(item.target, link);
      else if (item.type === "load") loadInIframe(item.url, link);
    });
    sidebar.appendChild(link);
  });
}

function showSection(sectionId, el = null) {
  document.getElementById('dashboardSection').style.display = (sectionId === 'dashboardSection') ? 'block' : 'none';
  contentFrame.style.display = 'none';
  loadingOverlay.style.display = 'none';
  updateActiveLink(el);
}

function loadInIframe(url, el = null) {
  document.getElementById('dashboardSection').style.display = 'none';
  loadingOverlay.style.display = 'flex';
  contentFrame.style.display = 'block';
  contentFrame.src = url + (url.includes('?') ? '&' : '?') + '_=' + Date.now();
  updateActiveLink(el);
}

function updateActiveLink(el) {
  document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
  if (el) el.classList.add('active');
}

contentFrame.addEventListener('load', () => loadingOverlay.style.display = 'none');
buildSidebar();
showSection('dashboardSection');
</script>








<?php
// PHP code ends here
?>
<script>
function updateStatus(dropdown){
  const id = dropdown.dataset.id;
  const type = dropdown.dataset.type;
  const status = dropdown.value;

  fetch("update.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}&type=${type}&status=${status}`
  })
  .then(res => res.text())
  .then(data => {
    if(data.trim() === "success"){
      alert("Status updated successfully");
    } else {
      alert("Failed to update status");
    }
  })
  .catch(err => {
    console.error(err);
    alert("Error updating status");
  });
}
</script>


</body>
</html>