<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php
// header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Saving Track</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body></body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">🌱 Smart Saving Track</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="members.php">Members</a></li>
        <li class="nav-item"><a class="nav-link" href="contributions.php">Contributions</a></li>
        <li class="nav-item"><a class="nav-link" href="loans.php">Loans</a></li>
        <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
        <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
      </ul>

      <div class="d-flex align-items-center">
        <span class="text-white me-3">
          👋 <?= isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : "Admin"; ?>
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </div>
</nav>
