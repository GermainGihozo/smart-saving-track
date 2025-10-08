<?php
session_start();
require_once "includes/db.php"; // Adjust path if needed

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($phone) || empty($password)) {
        $error = "Please enter both phone number and password.";
    } else {
        // Check if the user exists
        $stmt = $pdo->prepare("SELECT * FROM members WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Store session data
            $_SESSION['member_id'] = $user['id'];
            $_SESSION['member_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } elseif ($user['role'] === 'member') {
                header("Location: member/dashboard.php");
                exit;
            } else {
                $error = "Invalid user role configuration.";
            }
        } else {
            $error = "Invalid phone number or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Saving Track | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e3f2fd, #e8f5e9);
    }

    body {
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1; /* Takes up all space between header and footer */
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      overflow: hidden;
      width: 100%;
      max-width: 400px;
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .login-header {
      background: linear-gradient(90deg, #198754, #20c997);
      color: #fff;
      text-align: center;
      padding: 25px;
    }

    .login-header h3 {
      margin: 0;
      font-weight: 600;
    }

    .btn-login {
      background: #198754;
      color: #fff;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      background: #157347;
      transform: translateY(-2px);
    }

    footer {
      text-align: center;
      padding: 15px 0;
      color: #777;
      background-color: rgba(255, 255, 255, 0.5);
      font-size: 0.9rem;
      width: 100%;
      margin-top: auto;
    }
  </style>
</head>
<body>

  <main>
    <div class="login-card">
      <div class="login-header">
        <h3>Smart Saving Track</h3>
        <p>Secure Login</p>
      </div>
      <div class="p-4">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter your phone number" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
          </div>

          <button type="submit" class="btn btn-login w-100 py-2">Login</button>
        </form>
      </div>
    </div>
  </main>

  <footer>
    © <?= date('Y') ?> Smart Saving Track — All rights reserved.
  </footer>

</body>
</html>
