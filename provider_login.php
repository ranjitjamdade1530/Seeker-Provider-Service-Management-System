<?php
session_start();
include 'db.php';
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = "";

if (isset($_POST['login'])) {
    $provider_id = $_POST['provider_id']; 
    $password = $_POST['password'];

    $sql = "SELECT provider_id, provider_name, password, status FROM providers WHERE provider_id = '$provider_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $provider = $result->fetch_assoc();

        if ($provider['status'] === 'converted') {
            $error = "Your account has been converted as a Seeker. Please contact admin for login at: rjharsh4545@gmail.com";
        } elseif ($provider['password'] === $password) {
            if ($provider['status'] === 'active') {
                $_SESSION['provider_id'] = $provider['provider_id'];
                $_SESSION['provider_name'] = $provider['provider_name'];
                header("Location: provider_dashboard.php");
                exit();
            } else {
                $error = "Your account is not approved by admin yet.";
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Provider ID not found.";
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Provider Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f0f2f5; }
    .header img { width: 100%; height: 250px; }
    footer { background-color: #343a40; color: #fff; padding: 20px 0; text-align: center; }
    .login-box {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
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
          <li class="nav-item"><a class="nav-link active" href="provider_login.php">Provider Login</a></li>
          <li class="nav-item"><a class="nav-link" href="sicker_login.php">Seeker Login</a></li>
          <li class="nav-item"><a class="nav-link" href="add_sicker1.php">Register Seeker</a></li>
          <li class="nav-item"><a class="nav-link" href="add_provider1.php">Register Provider</a></li>
        </ul>
      </div>
    </div>
  </nav>
</div>

<!-- Login Form -->
<div class="login-box">
  <h4 class="text-center mb-4">Provider Login</h4>

  <form method="POST">
    <div class="mb-3">
      <label>Provider ID</label>
      <input type="text" name="provider_id" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
  </form>
</div>

<!-- Footer -->
<div class="container">
  <footer>
    &copy; <?= date('Y') ?> Service Management System.
  </footer>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript Alert for Error -->
<?php if (!empty($error)): ?>
  <script>
    alert("<?= addslashes($error) ?>");
  </script>
<?php endif; ?>

</body>
</html>
