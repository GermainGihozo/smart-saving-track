<?php
session_start();
require_once "../includes/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Check if the user exists
    $stmt = $pdo->prepare("SELECT * FROM members WHERE phone = ?");
    $stmt->execute([$phone]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($member && password_verify($password, $member['password'])) {
        // Set session
        $_SESSION['member_id'] = $member['id'];
        $_SESSION['member_name'] = $member['name'];
        $_SESSION['role'] = $member['role']; // assuming you have a 'role' column in your members table

        // Redirect based on role
        if ($member['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid phone number or password.";
    }
}
?>


<?php
//  include "../includes/header.php";

// header.php
?>

<!-- Header part -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Saving Track</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../index.php">💰 Smart Saving Track</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="../member/login.php">Member Login</a></li>
        <li class="nav-item"><a class="nav-link" href="../admin/login.php">Admin Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <h3 class="text-center text-success mb-4">📱 Member Login</h3>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3">
                <a href="../index.php">← Back to Home</a>
            </p>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
