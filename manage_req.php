<?php
session_start();
include 'db.php'; // adjust to your DB config

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

$provider_id = $_SESSION['provider_id'];
$provider_name = $_SESSION['provider_name'] ?? "Provider";






// Fetch only this provider's requests
$sql = "SELECT * FROM requests WHERE provider_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $provider_id); // ✅ Correct: "s" for string

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
      position: fixed;
      width: 250px;
      padding-top: 20px;
    }
    .sidebar a {
      color: white;
      padding: 10px 20px;
      display: block;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .main-content {
      margin-left: 250px;
      padding: 20px;
    }
    .navbar {
      margin-left: 250px;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h4 class="text-center text-white">Provider Panel</h4>
  <a href="provider_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="update_provider.php"><i class="bi bi-person-circle"></i> Profile</a>
  <a href="manage_req.php"><i class="bi bi-inbox"></i> Requests</a>
  <a href="logout1.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <span class="navbar-brand">Welcome, <?php echo htmlspecialchars($provider_name); ?></span>
  </div>
</nav>

<!-- Main Content -->
<div class="main-content">
  <div class="container">
    <h3 class="mb-4">Manage Requests</h3>

    <?php if ($result->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>User Name</th>
              <th>Mobile</th>
              <th>Type</th>
              <th>City</th>
              <th>Area</th>
              <th>Slot</th>
              <th>Status</th>
              <th>Rating</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['request_id']; ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['user_mobile']); ?></td>
                <td><?php echo htmlspecialchars($row['request_type']); ?></td>
                <td><?php echo htmlspecialchars($row['city']); ?></td>
                <td><?php echo htmlspecialchars($row['area']); ?></td>
                <td><?php echo htmlspecialchars($row['slot']); ?></td>
                <td class="status-text"><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['rating']); ?></td>
                <td>
                  <select class="form-select form-select-sm update-status" data-id="<?php echo $row['request_id']; ?>">
                    <option value="">Change</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                  </select>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info">No requests found.</div>
    <?php endif; ?>
  </div>
</div>

<script>
  $(document).ready(function () {
    $(".update-status").change(function () {
      var status = $(this).val();
      var requestId = $(this).data("id");

      if (status !== "") {
        $.ajax({
          url: "update_status.php",
          method: "POST",
          data: { request_id: requestId, status: status },
          success: function (response) {
            alert(response);
            location.reload(); // refresh the table
          },
          error: function () {
            alert("Failed to update status.");
          }
        });
      }
    });
  });
</script>

</body>
</html>
