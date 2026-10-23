<?php
session_start();

// DB connection
include 'db.php'; 

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sicker_id = $_POST['sicker_id'];
    $password = $_POST['password'];

    // Include status in SELECT
    $stmt = $conn->prepare("SELECT sicker_id, sicker_name, sicker_mobile, password, status 
                            FROM sickers 
                            WHERE sicker_id = ?");
    $stmt->bind_param("s", $sicker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ If you are storing hashed passwords, replace with:
        // if (password_verify($password, $user['password'])) {
        if ($user['password'] === $password) {

            if ($user['status'] === 'active') {
                // ✅ Approved user → start session
                $_SESSION['sicker_id'] = $user['sicker_id'];
                $_SESSION['sicker_name'] = $user['sicker_name'];
                $_SESSION['sicker_mobile'] = $user['sicker_mobile'];

                echo "<script>alert('Login Successful'); window.location='sicker_dashboard.php';</script>";
            } else {
                // ❌ Not approved yet
                echo "<script>alert('Your account is not approved by admin yet.');</script>";
            }

        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('Sicker ID not found');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sicker Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .header img { width: 100%; height: 250px; }
    footer { background-color: #343a40; color: #fff; padding: 20px 0; text-align: center; }
    .main-content { min-height: 400px; padding: 40px 20px; }
    .login-box { max-width: 400px; margin: 80px auto; padding: 30px; background: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    h2 { text-align: center; color: #007bff; margin-bottom: 25px; }
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
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin Login</a></li>
          <li class="nav-item"><a class="nav-link" href="provider_login.php">Provider Login</a></li>
          <li class="nav-item"><a class="nav-link active" href="sicker_login.php">Seeker Login</a></li>
          <li class="nav-item"><a class="nav-link" href="add_sicker1.php">Register Seeker</a></li>
          <li class="nav-item"><a class="nav-link" href="add_provider1.php">Register Provider</a></li>
        </ul>
      </div>
    </div>
  </nav>
</div> 

<div class="login-box">
  <h2>Seeker Login</h2>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Sicker ID</label>
      <input type="text" name="sicker_id" class="form-control" required >
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
  </form>
</div>

<!-- Footer -->
<div class="container">
  <footer>
    &copy; <?= date('Y') ?> Service Management System.
  </footer>
</div>

<!-- JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
