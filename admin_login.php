<?php
session_start();

// Set fixed admin credentials
$fixedAdminID = "admin";
$fixedPassword = "admin123";


// Handle login form submission
if (isset($_POST['login'])) {
    $admin_id = $_POST['admin_id'];
    $password = $_POST['password'];
   


    if ($admin_id === $fixedAdminID && $password === $fixedPassword) {
        $_SESSION['admin_id'] = $admin_id;
        header("Location: admin_dashboard.php"); // Redirect to admin dashboard
        exit();
    } else {
        $error = "Invalid Admin ID or Password!";
    }
}
?>
 
<!DOCTYPE html> 
<html>
<head>
    <title>Admin Login</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
         
    body { background-color: #f8f9fa; }
    .header img { width: 100%; height: 250px; }
    footer { background-color: #343a40; color: #fff; padding: 20px 0; text-align: center; }
    .main-content { min-height: 400px; padding: 40px 20px; }

        body {
            background: #f1f1f1; 
        }
        .login-box {
            max-width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #007bff;
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
          <li class="nav-item"><a class="nav-link" href="provider_login.php">Provider Login</a></li>
          <li class="nav-item"><a class="nav-link" href="sicker_login.php">Seeker Login</a></li>
          <li class="nav-item"><a class="nav-link" href="add_sicker1.php">Register Seeker</a></li>
          <li class="nav-item"><a class="nav-link" href="add_provider1.php">Register Provider</a></li>
        </ul>
      </div>
    </div>
  </nav>
</div>

<div class="login-box">
    <h2>Admin Login</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Admin ID</label>
            <input type="text" name="admin_id" class="form-control" required placeholder="Enter Admin ID">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Enter Password">
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

<!-- JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
